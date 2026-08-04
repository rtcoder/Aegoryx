const THEME_KEY = 'aegoryx.theme';
const THEMES = ['light', 'dark'];
const themePersistStates = new Map();

function validTheme(theme) {
    return THEMES.includes(theme);
}

function fallbackTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function storedTheme() {
    const theme = localStorage.getItem(THEME_KEY);

    return validTheme(theme) ? theme : null;
}

function currentTheme() {
    const htmlTheme = document.documentElement.dataset.theme;

    return validTheme(htmlTheme) ? htmlTheme : storedTheme() ?? fallbackTheme();
}

function applyTheme(theme) {
    const nextTheme = validTheme(theme) ? theme : fallbackTheme();

    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem(THEME_KEY, nextTheme);
    document.querySelectorAll('[data-theme-switcher]').forEach((switcher) => {
        switcher.dataset.currentTheme = nextTheme;
        switcher.querySelectorAll('[data-theme-value]').forEach((button) => {
            button.setAttribute('aria-pressed', button.dataset.themeValue === nextTheme ? 'true' : 'false');
        });
    });

    return nextTheme;
}

async function persistTheme(endpoint, theme) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(endpoint, {
        method: 'PATCH',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
        },
        body: JSON.stringify({ theme }),
    });

    if (!response.ok) {
        throw new Error(`Theme update failed with ${response.status}`);
    }

    return response.json();
}

function statusSwitchersForEndpoint(endpoint, state) {
    document.querySelectorAll('[data-theme-switcher]').forEach((switcher) => {
        if (switcher.dataset.themeEndpoint === endpoint) {
            state.switchers.add(switcher);
        }
    });

    return state.switchers;
}

function updateThemeStatus(endpoint, state, status) {
    statusSwitchersForEndpoint(endpoint, state).forEach((switcher) => {
        switcher.dataset.themeStatus = status;
    });
}

function persistLatestTheme(endpoint, theme, switcher) {
    const state = themePersistStates.get(endpoint) ?? {
        inFlight: false,
        latestTheme: null,
        persistedTheme: null,
        switchers: new Set(),
    };

    state.latestTheme = theme;
    state.switchers.add(switcher);
    themePersistStates.set(endpoint, state);
    updateThemeStatus(endpoint, state, 'saving');

    if (!state.inFlight) {
        processThemePersistQueue(endpoint, state);
    }
}

async function processThemePersistQueue(endpoint, state) {
    state.inFlight = true;

    while (state.persistedTheme !== state.latestTheme) {
        const savingTheme = state.latestTheme;

        try {
            await persistTheme(endpoint, savingTheme);
            state.persistedTheme = savingTheme;

            if (state.latestTheme === savingTheme) {
                updateThemeStatus(endpoint, state, 'saved');
            }
        } catch {
            if (state.latestTheme === savingTheme) {
                updateThemeStatus(endpoint, state, 'error');
                break;
            }
        }
    }

    state.inFlight = false;
}

applyTheme(currentTheme());

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-value]');

    if (!button) {
        return;
    }

    const switcher = button.closest('[data-theme-switcher]');
    const theme = button.dataset.themeValue;

    if (!switcher || !validTheme(theme)) {
        return;
    }

    const nextTheme = applyTheme(theme);
    const endpoint = switcher.dataset.themeEndpoint;

    if (!endpoint) {
        switcher.dataset.themeStatus = 'saved';
        return;
    }

    persistLatestTheme(endpoint, nextTheme, switcher);
});

window.aegoryxTheme = {
    set(theme, options = {}) {
        return applyTheme(theme);
    },
    clear() {
        localStorage.removeItem(THEME_KEY);
        return applyTheme(fallbackTheme());
    },
};
