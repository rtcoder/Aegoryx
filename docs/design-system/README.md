# Design System Aegoryx

Ten dokument opisuje wspólny system UI dla panelu landlorda i panelu tenantowego. Nowe widoki Blade/Livewire powinny korzystać z tych tokenów i komponentów zamiast kopiować długie klasy Tailwinda.

## Zasada główna

UI Aegoryx ma być robocze, czytelne i spokojne. To panel operacyjny dla CMS/CRM, więc priorytetem są:

- szybkie skanowanie danych,
- powtarzalne formularze,
- wyraźne akcje,
- dobra czytelność w light i dark mode,
- brak ozdobników, które nie pomagają użytkownikowi wykonać pracy.

## Motywy

Motyw działa przez `data-theme` na elemencie `html`.

```html
<html data-theme="light">
<html data-theme="dark">
```

`resources/js/app.js` ustawia motyw z `localStorage` pod kluczem `aegoryx.theme`, a jeśli nie ma zapisu, używa preferencji systemowej.

Do ręcznego przełączenia można użyć:

```js
window.aegoryxTheme.set('light')
window.aegoryxTheme.set('dark')
window.aegoryxTheme.clear()
```

Nie używamy sztywnych kolorów typu `bg-neutral-950` w nowych komponentach. Zamiast tego korzystamy z klas systemowych albo CSS variables z `resources/css/app.css`.

## Tokeny

Najważniejsze tokeny:

- `--ui-bg` - tło aplikacji,
- `--ui-surface` - główne powierzchnie kart i paneli,
- `--ui-surface-muted` - pola formularzy, nagłówki tabel, ciche sekcje,
- `--ui-border` - standardowe obramowania,
- `--ui-border-strong` - mocniejszy border dla hover/akcentu,
- `--ui-text` - tekst podstawowy,
- `--ui-text-muted` - tekst pomocniczy,
- `--ui-text-subtle` - podpisy, metadane, puste stany,
- `--ui-accent` - akcje podstawowe i linki,
- `--ui-success`, `--ui-warning`, `--ui-danger` - stany semantyczne,
- `--ui-focus` - focus ring.

## Typografia

Używamy klas:

```blade
<h1 class="ui-heading-1">...</h1>
<h2 class="ui-heading-2">...</h2>
<p class="ui-body">...</p>
<p class="ui-caption">...</p>
```

W panelach nie używamy hero-typografii. Nagłówki mają pomagać w skanowaniu, nie dominować ekran.

## Przyciski

Używamy `x-ui.button`.

```blade
<x-ui.button type="submit">
    {{ __('common.save') }}
</x-ui.button>

<x-ui.button :href="route('tenant.crm.index')" variant="secondary" wire:navigate>
    {{ __('common.back') }}
</x-ui.button>

<x-ui.button type="submit" variant="danger" size="sm">
    {{ __('common.delete') }}
</x-ui.button>
```

Warianty:

- `primary` - główna akcja na ekranie,
- `secondary` - akcja poboczna,
- `ghost` - cicha akcja w toolbarze lub nawigacji,
- `danger` - usuwanie albo destrukcyjne operacje.

Rozmiary:

- `md` - domyślny,
- `sm` - tabele, listy, kompaktowe toolbary.

## Karty

Używamy `x-ui.card` dla pojedynczych paneli, formularzy i powtarzalnych bloków.

```blade
<x-ui.card :title="__('crm.create_contact')" :subtitle="__('crm.contacts_description')">
    ...
</x-ui.card>
```

Karty nie powinny być zagnieżdżane w kartach. Jeśli potrzebna jest sekcja w środku, użyj `ui-muted-panel`.

## Formularze

Używamy komponentów:

```blade
<x-form.input name="email" type="email" :label="__('common.email')" />
<x-form.textarea name="notes" :label="__('crm.notes')" />
<x-form.select name="status" :label="__('common.status')">
    <option value="active">...</option>
</x-form.select>
<x-form.checkbox name="enabled" :label="__('features.enabled')" />
```

Komponenty formularzy obsługują:

- `old()`,
- błędy walidacji,
- `help`,
- własne `id`,
- atrybuty Livewire, np. `wire:model`.

Nie dopisujemy ręcznie powtarzalnych bloków `label + input + @error`.

## Badge

Używamy `x-ui.badge`.

```blade
<x-ui.badge variant="success">
    {{ __('common.active') }}
</x-ui.badge>
```

Warianty:

- `neutral`,
- `accent`,
- `success`,
- `warning`,
- `danger`.

## Tabele

Dla tabel używamy klasy `ui-table`, a kontener z przewijaniem zostaje w widoku.

```blade
<div class="overflow-x-auto">
    <table class="ui-table">
        ...
    </table>
</div>
```

Akcje w tabelach powinny być krótkie i powtarzalne: link zarządzania, przycisk statusu, usuwanie jako `danger`.

## Layout

Body paneli powinno używać:

```blade
<body class="ds-app antialiased">
```

Główny shell:

```blade
<div class="ds-shell flex">
```

Nowe powierzchnie powinny korzystać z `ui-card`, `ui-muted-panel`, `ui-divider` i tokenów, a nie z twardych klas kolorystycznych.

## Zasady dla nowych widoków

- Nowy tekst widoczny w UI dodajemy do wszystkich języków: `pl`, `en`, `de`, `es`, `ru`, `fr`.
- Nowe formularze budujemy z komponentów `x-form.*`.
- Nowe akcje budujemy z `x-ui.button`.
- Stany statusu pokazujemy przez `x-ui.badge`.
- Nie tworzymy osobnych palet per moduł.
- Nie używamy dekoracyjnych gradientów, orbów ani hero-sekcji w panelach.
- Jeśli trzeba dodać nowy komponent UI, najpierw sprawdzamy, czy da się rozszerzyć istniejący.
