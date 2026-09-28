<?php

namespace App\Services;

use App\Models\Addon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use ZipArchive;

class ModuleService
{
    /**
     * Map add-on names to their module folder / class name.
     */
    public function getModuleName(string $addonName): string
    {
        return match ($addonName) {
            'Embedded Signup' => 'EmbeddedSignup',
            'AI Assistant' => 'IntelliReply',
            'Webhooks' => 'Webhook',
            'Flow builder' => 'FlowBuilder',
            'Razorpay' => 'Razorpay',
            'Google Recaptcha' => 'Recaptcha',
            'Google Analytics' => 'Analytics',
            'Google Maps' => 'Maps',
            default => str_replace(' ', '', ucwords($addonName)),
        };
    }

    /**
     * Determine if the add-on has a built-in core implementation in Wappiyo.
     */
    public function hasBuiltinImplementation(string $addonName): bool
    {
        return in_array($addonName, [
            'Embedded Signup',
            'AI Assistant',
            'Webhooks',
            'Flow builder',
            'Razorpay',
            'Google Recaptcha',
            'Google Analytics',
            'Google Maps',
        ], true);
    }

    /**
     * Return default configuration metadata (input fields) for built-in add-ons.
     */
    public function getBuiltinMetadata(string $addonName): array
    {
        return match ($addonName) {
            'Embedded Signup' => [
                'input_fields' => [
                    ['element' => 'input', 'type' => 'text', 'name' => 'whatsapp_client_id', 'label' => 'Facebook App ID', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'password', 'name' => 'whatsapp_client_secret', 'label' => 'Facebook App Secret', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'text', 'name' => 'whatsapp_config_id', 'label' => 'Embedded Signup Config ID', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'text', 'name' => 'whatsapp_callback_token', 'label' => 'Webhook Verify Token', 'class' => 'col-span-2'],
                    ['element' => 'toggle', 'type' => 'checkbox', 'name' => 'is_embedded_signup_active', 'label' => 'Activate Embedded Signup', 'class' => 'col-span-2'],
                ]
            ],
            'AI Assistant' => [
                'input_fields' => [
                    ['element' => 'input', 'type' => 'password', 'name' => 'openai_api_key', 'label' => 'OpenAI API Key', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'text', 'name' => 'openai_model', 'label' => 'OpenAI Model (e.g. gpt-4o-mini)', 'class' => 'col-span-2'],
                    ['element' => 'toggle', 'type' => 'checkbox', 'name' => 'ai_assistant', 'label' => 'Enable AI Assistant Bot', 'class' => 'col-span-2'],
                ]
            ],
            'Webhooks' => [
                'input_fields' => [
                    ['element' => 'toggle', 'type' => 'checkbox', 'name' => 'webhook', 'label' => 'Enable Outbound Webhooks', 'class' => 'col-span-2'],
                ]
            ],
            'Flow builder' => [
                'input_fields' => [
                    ['element' => 'toggle', 'type' => 'checkbox', 'name' => 'flow_builder', 'label' => 'Enable Visual Flow Builder', 'class' => 'col-span-2'],
                ]
            ],
            'Razorpay' => [
                'input_fields' => [
                    ['element' => 'input', 'type' => 'text', 'name' => 'razorpay_key_id', 'label' => 'Key ID', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'text', 'name' => 'razorpay_secret_key', 'label' => 'Secret Key', 'class' => 'col-span-2'],
                    ['element' => 'input', 'type' => 'text', 'name' => 'razorpay_webhook_secret', 'label' => 'Webhook Secret', 'class' => 'col-span-2'],
                    ['element' => 'toggle', 'type' => 'checkbox', 'name' => 'razorpay_active', 'label' => 'Enable Razorpay', 'class' => 'col-span-2'],
                ]
            ],
            default => ['input_fields' => []],
        };
    }

    /**
     * Main installer entrypoint.
     * Supports:
     * 1. Local module directory in modules/{Module}
     * 2. Local ZIP packages in modules/{Module}.zip or modules/addon.zip
     * 3. Locally bundled core add-ons
     * 4. Remote vendor server download when an Envato purchase code is provided
     */
    public function install(Request $request)
    {
        $addonName = $request->input('addon');
        $uuid = $request->input('uuid');
        $purchaseCode = trim((string)$request->input('purchase_code', ''));
        $module = $this->getModuleName($addonName);

        // 1. Check if a local module ZIP archive is present and extract it
        $localZip = base_path("modules/{$module}.zip");
        $genericZip = base_path('modules/addon.zip');

        if (file_exists($localZip)) {
            $this->extractZip($localZip);
        } elseif (file_exists($genericZip)) {
            $this->extractZip($genericZip);
        }

        // 2. Check if the module folder exists locally in modules/{Module}
        $localModuleDir = base_path("modules/{$module}");
        if (is_dir($localModuleDir)) {
            return $this->installLocalModule($module, $uuid, $addonName);
        }

        // 3. Check if this add-on is already bundled as a native core implementation
        if ($this->hasBuiltinImplementation($addonName)) {
            return $this->installBuiltinAddon($addonName, $uuid);
        }

        // 4. Remote Vendor Download Flow (Requires valid purchase code)
        if (empty($purchaseCode)) {
            return Redirect::back()->withErrors([
                'purchase_code' => __('No local package found in modules/ directory. An authorized Envato purchase code is required to download this add-on from the vendor.')
            ])->withInput();
        }

        $zipFilePath = base_path('modules/addon.zip');
        try {
            $this->downloadFromVendor($purchaseCode, $addonName, $zipFilePath);
            $this->extractZip($zipFilePath);
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }

            $metadata = $this->fetchVendorMetadata($purchaseCode, $addonName);
            Addon::where('uuid', $uuid)->update([
                'metadata' => is_array($metadata) ? json_encode($metadata) : $metadata,
                'status' => 1
            ]);

            Log::info("Addon {$addonName} successfully installed from remote vendor.");

            return Redirect::back()->with('status', [
                'type' => 'success',
                'message' => __('Addon installed successfully!')
            ]);
        } catch (RequestException $e) {
            return $this->handleRequestException($e, $zipFilePath);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e, $zipFilePath);
        }
    }

    /**
     * Install an add-on from a locally extracted module directory.
     */
    protected function installLocalModule(string $module, string $uuid, string $addonName)
    {
        try {
            // Run module migrations if present
            $migrationPath = "modules/{$module}/Database/Migrations";
            if (is_dir(base_path($migrationPath))) {
                Artisan::call('migrate', ['--path' => $migrationPath, '--force' => true]);
            }

            // Run module seeders if present
            $seederClass = "Modules\\{$module}\\Database\\Seeders\\{$module}Seeder";
            if (class_exists($seederClass)) {
                Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
            }

            // Run SetupService if defined by module
            $setupClass = "Modules\\{$module}\\Services\\SetupService";
            if (class_exists($setupClass)) {
                (new $setupClass())->index();
            }

            // Read metadata from manifest or built-in defaults
            $manifestFile = base_path("modules/{$module}/module.json");
            $metadata = file_exists($manifestFile)
                ? file_get_contents($manifestFile)
                : json_encode($this->getBuiltinMetadata($addonName));

            Addon::where('uuid', $uuid)->update([
                'metadata' => $metadata,
                'status' => 1
            ]);

            Log::info("Local module {$module} installed successfully for addon {$addonName}.");

            return Redirect::back()->with('status', [
                'type' => 'success',
                'message' => __('Local add-on installed successfully!')
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to install local module {$module}: " . $e->getMessage());
            return Redirect::back()->withErrors([
                'purchase_code' => __('Failed to install local module: ') . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Install an add-on that has a built-in core implementation.
     */
    protected function installBuiltinAddon(string $addonName, string $uuid)
    {
        try {
            $metadata = json_encode($this->getBuiltinMetadata($addonName));

            Addon::where('uuid', $uuid)->update([
                'metadata' => $metadata,
                'status' => 1
            ]);

            Log::info("Built-in addon {$addonName} installed and enabled successfully.");

            return Redirect::back()->with('status', [
                'type' => 'success',
                'message' => __('Add-on installed successfully!')
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to enable built-in addon {$addonName}: " . $e->getMessage());
            return Redirect::back()->withErrors([
                'purchase_code' => __('Failed to enable add-on: ') . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Download the add-on ZIP package from the vendor's official server.
     */
    protected function downloadFromVendor(string $purchaseCode, string $addonName, string $destinationZip)
    {
        $client = new Client();
        $response = $client->post('https://axis96.com/api/install/addon', [
            'form_params' => [
                'purchase_code' => $purchaseCode,
                'addon' => $addonName,
            ],
            'headers' => [
                'Referer' => url('/'),
            ],
            'sink' => $destinationZip,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(__('Failed to download the addon from vendor server.'));
        }
    }

    /**
     * Retrieve add-on metadata and execute module setup service from vendor server.
     */
    protected function fetchVendorMetadata(string $purchaseCode, string $addonName)
    {
        $client = new Client();
        $response = $client->post('https://axis96.com/api/install/addon/setup', [
            'form_params' => [
                'purchase_code' => $purchaseCode,
                'addon' => $addonName,
            ],
            'headers' => [
                'Referer' => url('/'),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(__('Failed to retrieve addon metadata from vendor server.'));
        }

        $payload = json_decode($response->getBody()->getContents(), true);
        if (!isset($payload['success']) || !$payload['success']) {
            throw new \Exception(__('Failed to retrieve valid metadata from vendor server.'));
        }

        $moduleName = $payload['module'] ?? '';
        $setupClass = "Modules\\{$moduleName}\\Services\\SetupService";
        if (class_exists($setupClass)) {
            (new $setupClass())->index();
        }

        return $payload['data'] ?? [];
    }

    /**
     * Extract a ZIP archive to the base modules directory safely.
     */
    protected function extractZip(string $zipPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception(__('Failed to open the addon zip archive.'));
        }

        $modulesDir = base_path('modules');
        if (!is_dir($modulesDir)) {
            mkdir($modulesDir, 0755, true);
        }

        $zip->extractTo($modulesDir);
        $zip->close();
    }

    /**
     * Handle HTTP request exceptions during remote downloads.
     */
    protected function handleRequestException(RequestException $e, string $zipFilePath)
    {
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        if ($e->hasResponse()) {
            $body = (string) $e->getResponse()->getBody();
            $decoded = json_decode($body);
            return Redirect::back()->withErrors([
                'purchase_code' => $decoded->message ?? __('An error occurred while contacting the vendor license server.')
            ])->withInput();
        }

        return Redirect::back()->withErrors([
            'purchase_code' => __('Unable to reach the vendor activation server: ') . $e->getMessage()
        ])->withInput();
    }

    /**
     * Handle general exceptions during installation.
     */
    protected function handleGeneralException(\Exception $e, string $zipFilePath)
    {
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        return Redirect::back()->withErrors([
            'purchase_code' => $e->getMessage()
        ])->withInput();
    }
}
