# Wappiyo Design System — Vibrant Intelligence

This reference guide documents the core tokens, components, and UX patterns used across the **Wappiyo** platform.

---

## 1. Brand & Semantic Color Tokens

### Primary Palette
- **Primary / Brand**: `#6C5CE7` (`rgb(108, 92, 231)`) — Dominant interactive color, primary actions, active navigation states.
- **Violet**: `#8B5CF6` (`rgb(139, 92, 246)`) — Secondary brand gradient stop, accent highlights.
- **Cyan**: `#06B6D4` (`rgb(6, 182, 212)`) — Secondary accents, automated flows, real-time indicators.
- **Pink**: `#EC4899` (`rgb(236, 72, 153)`) — Campaigns, broadcast accents, broadcast drivers.

### Semantic Status
- **Success / Connected / Active**: `#22C55E` (`rgb(34, 197, 94)`) — WhatsApp online status, completed automations, paid invoices.
- **Warning / Pending**: `#F97316` (`rgb(249, 115, 22)`) — Scheduled broadcasts, review templates, trial warnings.
- **Danger / Destructive / Expired**: `#EF4444` (`rgb(239, 68, 68)`) — Delete actions, payment past-due, expired trials.

### Dark Mode Architecture
- **Canvas / Root Background**: `#09090B` (zinc-950 deep black)
- **Surface / Card Background**: `#111113` (elevated surface)
- **Nested Elevate / Input Background**: `#18181B` (zinc-900)
- **Subtle Borders**: `#27272A` (zinc-800)
- **Muted Text**: `#71717A` (zinc-500)
- **Secondary Text**: `#A1A1AA` (zinc-400)
- **Primary Text**: `#FAFAFA` (zinc-50)

---

## 2. Typography Scale

- **Font Family**:
  - Headings & Primary UI: `'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif`
  - Code & Tokens: `'JetBrains Mono', 'ui-monospace', monospace`
- **Hierarchies**:
  - `Page Title`: `text-2xl font-extrabold tracking-tight`
  - `Section Title`: `text-lg font-bold tracking-tight`
  - `Card Header`: `text-base font-semibold`
  - `Body`: `text-sm font-normal text-slate-600 dark:text-zinc-400`
  - `Label`: `text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400`
  - `Caption`: `text-[11px] text-slate-400 dark:text-zinc-500`

---

## 3. Elevation & Shadows

- **Subtle Card**: `border border-slate-200/80 dark:border-zinc-800 shadow-sm`
- **Elevated Dropdown / Popover**: `shadow-xl shadow-slate-900/5 dark:shadow-black/50`
- **Active / Primary CTA Glow**: `shadow-md shadow-indigo-500/20`
- **Modal Backdrop**: `bg-black/60 backdrop-blur-xs`

---

## 4. Reusable UI Components Directory (`resources/js/Components/UI/`)

| Component | Purpose & Standard Variants |
|---|---|
| `Button.vue` | `primary`, `secondary`, `outline`, `ghost`, `danger` variants with built-in loading spinner |
| `IconButton.vue` | Circular / rounded icon triggers with tooltips |
| `Badge.vue` | Pill badges (`emerald`, `purple`, `rose`, `amber`, `slate`) |
| `Card.vue` | Consistent container card with header, body, footer slots |
| `StatCard.vue` | Metric overview cards with KPI value, trend delta, and gradient icon container |
| `EmptyState.vue` | Structured empty state with icon, title, description, and action CTA |
| `Skeleton.vue` | Progressive shimmer loading placeholder |
| `Drawer.vue` | Slide-over drawer with Escape key support, backdrop blur, and focus management |
| `Modal.vue` | HeadlessUI dialog with smooth transition, focus trap, and responsive width |
| `Tabs.vue` | Tab list with animated active pill indicator |
| `CommandPalette.vue` | Global `⌘K` or `Ctrl+K` searchable command menu across all modules |

---

## 5. Standardized Terminology

- **Contacts**: Individual customer CRM records with phone, tags, attributes, and groups.
- **Conversations**: WhatsApp chat threads with message history and agent assignments.
- **Templates**: WhatsApp Cloud API approved message templates with parameters.
- **Campaigns**: One-time broadcasts or scheduled messaging blasts to targeted contact segments.
- **Automations**: Inbound reply priority, flow builder paths, and AI reply assistant.
- **Tickets**: Support requests with states (`Open`, `Pending`, `Resolved`, `Closed`).
- **Organization**: Multi-tenant workspace scoped to session context (`session('current_organization')`).
