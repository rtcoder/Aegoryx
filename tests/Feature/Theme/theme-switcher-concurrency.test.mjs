import assert from 'node:assert/strict';
import { pathToFileURL } from 'node:url';
import test from 'node:test';

function deferred() {
    let resolve;
    let reject;

    const promise = new Promise((resolvePromise, rejectPromise) => {
        resolve = resolvePromise;
        reject = rejectPromise;
    });

    return { promise, resolve, reject };
}

function createButton(theme) {
    return {
        attributes: new Map(),
        dataset: { themeValue: theme },
        setAttribute(name, value) {
            this.attributes.set(name, value);
        },
        closest(selector) {
            if (selector === '[data-theme-value]') {
                return this;
            }

            if (selector === '[data-theme-switcher]') {
                return this.switcher;
            }

            return null;
        },
    };
}

function createThemeHarness() {
    const buttons = [createButton('light'), createButton('dark')];
    const switcher = {
        dataset: {
            currentTheme: 'light',
            themeEndpoint: '/theme',
        },
        querySelectorAll(selector) {
            return selector === '[data-theme-value]' ? buttons : [];
        },
    };

    buttons.forEach((button) => {
        button.switcher = switcher;
    });

    const listeners = new Map();
    const requests = [];

    globalThis.localStorage = {
        items: new Map(),
        getItem(key) {
            return this.items.get(key) ?? null;
        },
        setItem(key, value) {
            this.items.set(key, value);
        },
        removeItem(key) {
            this.items.delete(key);
        },
    };

    globalThis.window = {
        matchMedia() {
            return { matches: false };
        },
    };

    globalThis.document = {
        documentElement: { dataset: {} },
        addEventListener(type, listener) {
            listeners.set(type, listener);
        },
        querySelector(selector) {
            if (selector === 'meta[name="csrf-token"]') {
                return { getAttribute: () => 'csrf-token' };
            }

            return null;
        },
        querySelectorAll(selector) {
            return selector === '[data-theme-switcher]' ? [switcher] : [];
        },
    };

    globalThis.fetch = (endpoint, options) => {
        const request = deferred();

        requests.push({ endpoint, options, request });

        return request.promise;
    };

    return {
        buttons,
        requests,
        switcher,
        click(button) {
            listeners.get('click')({ target: button });
        },
    };
}

async function settlePromises() {
    await new Promise((resolve) => {
        setImmediate(resolve);
    });
}

test('theme persistence serializes in-flight updates and writes the latest choice last', async () => {
    const harness = createThemeHarness();
    const appUrl = `${pathToFileURL(`${process.cwd()}/resources/js/app.js`).href}?test=${Date.now()}`;

    await import(appUrl);

    harness.click(harness.buttons[1]);
    harness.click(harness.buttons[0]);

    assert.equal(document.documentElement.dataset.theme, 'light');
    assert.equal(localStorage.getItem('aegoryx.theme'), 'light');
    assert.equal(harness.switcher.dataset.themeStatus, 'saving');
    assert.equal(harness.requests.length, 1);
    assert.equal(JSON.parse(harness.requests[0].options.body).theme, 'dark');

    harness.requests[0].request.resolve({ ok: true, json: async () => ({ theme: 'dark' }) });
    await settlePromises();

    assert.equal(harness.switcher.dataset.themeStatus, 'saving');
    assert.equal(harness.requests.length, 2);
    assert.equal(JSON.parse(harness.requests[1].options.body).theme, 'light');

    harness.requests[1].request.resolve({ ok: true, json: async () => ({ theme: 'light' }) });
    await settlePromises();

    assert.equal(harness.switcher.dataset.themeStatus, 'saved');
});
