<template>
    <div>

        <section id="section1" class="pt-2 md:pt-3 px-5 md:px-10 lg:px-20 2xl:px-60 relative">
            <div class="flex justify-between items-center">
                <Link href="/" class="inline-block">
                    <img class="max-w-[150px]" v-if="props.companyConfig.logo" :src="'/media/' + props.companyConfig.logo" :alt="props.companyConfig.company_name">
                    <h1 v-else class="text-2xl">{{ props.companyConfig.company_name }}</h1>
                </Link>
                <div class="hidden lg:flex justify-center items-center text-md space-x-8">
                    <Link href="/#section2" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Features') }}</Link>
                    <Link href="/#section4" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Pricing') }}</Link>
                    <Link href="/#section5" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Reviews') }}</Link>
                    <Link href="/#section6" class="cursor-pointer hover:border-b-2 border-black">{{ $t('FAQs') }}</Link>
                    <Link href="/#section7" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Contact us') }}</Link>
                </div>
                
                <div class="flex justify-end items-center text-md">
                    <Link href="/login" class="hidden md:inline-block mr-10 cursor-pointer hover:text-gray-400 border-black">{{ $t('Login') }}</Link>
                    <Link href="/signup" class="hidden md:inline-block bg-primary hover:bg-secondary text-white p-2 rounded-lg text-sm w-fit px-8">{{ $t('Sign Up') }}</Link>
                    <Link href="/login" class="md:hidden bg-primary hover:bg-secondary text-white p-2 rounded-lg text-sm flex w-fit px-8">{{ $t('Login') }}</Link>
                </div>
            </div>
            <hr style="margin-top:0%;margin-bottom:1%;" />
            <div class="h-full">
                <h1 class="text-center text-3xl">{{ props.page.data.name }}</h1>
                <h2 class="text-center">Last Updated: {{ props.page.data.updated_at }}</h2>
                <div class="mt-4 px-40 mb-20" v-html="props.page.data.content"></div>
            </div>
        </section>

        <footer id="section7" class="bg-gray-300/10 px-5 md:px-10 lg:px-20 md:pt-10 pb-5 2xl:px-60">
            <div class="md:flex">
                <div class="md:w-1/4 lg:w-1/2">
                    <div>
                        <img class="max-w-[150px]" v-if="props.companyConfig.logo" :src="'/media/' + props.companyConfig.logo" :alt="props.companyConfig.company_name">
                        <h4 v-else class="text-2xl mb-2">{{ props.companyConfig.company_name }}</h4>
                        <div style="margin-right: 21%;margin-top: 20px;">
                        {{ $t('With WhatsApp.io, we ensure that you never miss a message. Our team is available to chat, resolve your queries, and assist you in real time.')}}
                        </div>
                    </div>
                </div>
                <div class="md:w-3/4 lg:w-1/2 md:flex md:justify-between text-sm space-y-8 md:space-y-0">
                    <div>
                        <h4 class="text-xl font-regular mb-2">{{ $t('Links') }}</h4>
                        <div class="flex md:justify-between space-x-10 md:space-x-10 lg:space-x-20 space-y-2 md:space-y-0">
                            <div class="space-y-2">
                                <div>
                                    <a href="#section2" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Features') }}</a>
                                </div>
                                <div>
                                    <a href="#section3" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('How it works') }}</a>
                                </div>
                                <div>
                                    <a href="#section4" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Pricing') }}</a>
                                </div>
                                <div>
                                    <a href="#section5" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Reviews') }}</a>
                                </div>
                                <div>
                                    <a href="#section6" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('FAQs') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <h4 class="text-xl font-regular mb-2">{{ $t('Information') }}</h4>
                            <div v-if="pages.length > 0" class="space-y-2">
                                <div v-for="page in pages" :key="page.id">
                                    <Link :href="'/pages/' + formattedName(page.name)" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t(page.name) }}</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t pt-5 text-sm">
                <p class="font-light">{{ $t('Copyright') }} {{ currentYear }}. {{ $t('All rights reserved') }}.</p>
            </div>
        </footer>

        <!-- Floating WhatsApp Icon -->
        <a href="https://wa.me/919916225399?text=Hi%2C%20I%20am%20interested" target="_blank" class="fixed bottom-5 right-5 bg-green-500 text-white p-3 rounded-full shadow-lg hover:bg-green-600 transition duration-300">
            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 2.5C8.544 2.5 2.5 8.544 2.5 16c0 2.75.732 5.453 2.108 7.844L2 30l6.309-2.553A13.373 13.373 0 0 0 16 29.5C23.456 29.5 29.5 23.456 29.5 16S23.456 2.5 16 2.5Zm0 2c6.69 0 12.5 5.81 12.5 12.5S22.69 27.5 16 27.5a11.44 11.44 0 0 1-5.963-1.65l-.429-.256-3.875 1.568 1.49-3.727-.276-.44A10.46 10.46 0 0 1 5.5 16C5.5 9.31 11.31 4.5 16 4.5Zm-4.025 7.22c-.276 0-.588.008-.912.035a1.36 1.36 0 0 0-.964.453c-.304.34-.988.964-.988 2.34 0 1.376.656 2.713.752 2.9.096.188 1.296 2.6 3.276 3.744 1.98 1.144 2.376 1.036 2.808.968.432-.068 1.38-.56 1.58-1.104.2-.544.2-1.012.144-1.104-.056-.092-.216-.144-.448-.252s-1.38-.68-1.596-.76c-.216-.08-.372-.12-.528.12-.156.24-.608.76-.748.92s-.276.18-.516.06c-.24-.12-.96-.36-1.836-1.144-.68-.608-1.144-1.364-1.28-1.6s-.014-.36.092-.48c.104-.112.228-.24.34-.36s.22-.24.336-.384c.112-.144.072-.264.048-.36-.024-.096-.528-1.32-.72-1.808-.192-.488-.384-.4-.528-.4Z"/>
            </svg>
        </a>
    </div>
</template>
<script setup>
    import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
    import { Link } from "@inertiajs/vue3";
    import LangToggle from '@/Components/LangToggle.vue';

    const props = defineProps({ page: Object, companyConfig: Object, languages: Object, currentLanguage:String, currency: String, pages: Object });

    const currentYear = new Date().getFullYear();
    const facebookUrl = ref(null);
    const twitterUrl = ref(null);
    const instagramUrl = ref(null);
    const slackUrl = ref(null);
    const linkedinUrl = ref(null);

    const parseSocials = () => {
        try {
            const socialsArray = JSON.parse(props.companyConfig.socials);

            facebookUrl.value = socialsArray['facebook'] || null;
            twitterUrl.value = socialsArray['twitter'] || null;
            instagramUrl.value = socialsArray['instagram'] || null;
            slackUrl.value = socialsArray['slack'] || null;
            linkedinUrl.value = socialsArray['linkedin'] || null;
        } catch (error) {
            console.error('Error parsing socials:', error);
        }
    };

    const getDetail = (value, key) => {
        if(value){
            const item = JSON.parse(value);
            return item?.[key] ?? null;
        } else {
            return null;
        }
    }

    const formattedName = computed(() => {
        return (value) => {
            return value.trim().toLowerCase().replace(/\s+/g, '-');
        };
    });

    const selectedFeature = ref(1);
    const selectedFaq = ref(1);

    onMounted(() => {
        parseSocials();
    });
</script>