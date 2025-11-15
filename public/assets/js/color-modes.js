(() => {
    'use strict'

    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    // Tidak pakai sistem lagi, default hardcoded: 'light'
    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()

        if (storedTheme === 'light' || storedTheme === 'dark') {
            return storedTheme
        }

        // First time visit / value aneh -> paksa 'light'
        const defaultTheme = 'light'
        setStoredTheme(defaultTheme)
        return defaultTheme
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    // --- Inisialisasi theme (prioritas: query param > localStorage > default light) ---
    const urlParams = new URLSearchParams(window.location.search)
    const themeParam = urlParams.get('theme')

    let activeTheme

    if (themeParam === 'light' || themeParam === 'dark') {
        activeTheme = themeParam
        setStoredTheme(activeTheme)
    } else {
        activeTheme = getPreferredTheme()
    }

    setTheme(activeTheme)

    const showActiveTheme = (theme) => {
        const themeSwitcher = document.querySelector('#theme-switcher')

        if (!themeSwitcher) {
            return
        }

        const box = document.querySelector('.box')

        if (box) {
            if (theme === 'dark') {
                box.classList.remove('light')
                box.classList.add('dark')
            } else {
                box.classList.remove('dark')
                box.classList.add('light')
            }
        }
    }

    // HAPUS: listener matchMedia (tidak follow sistem lagi)
    // window.matchMedia('(prefers-color-scheme: dark)').addEventListener(...)

    window.addEventListener('DOMContentLoaded', () => {
        const currentTheme = getPreferredTheme()
        showActiveTheme(currentTheme)

        const themeSwitcher = document.querySelector('#theme-switcher')

        if (themeSwitcher) {
            // Sync posisi switch pertama kali
            themeSwitcher.checked = (currentTheme === 'dark')

            themeSwitcher.addEventListener('change', function () {
                const theme = this.checked ? 'dark' : 'light'
                setStoredTheme(theme)
                setTheme(theme)
                showActiveTheme(theme)
            })
        }
    })

    // Bagian ini tidak perlu lagi karena sudah di-handle di atas:
    // const urlParams = new URLSearchParams(window.location.search);
    // ...

})()
