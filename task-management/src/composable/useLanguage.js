import { useI18n } from 'vue-i18n'

export function useLanguage() {
  const { locale } = useI18n()

  const setHtmlAttributes = (lang) => {
    document.documentElement.setAttribute(
      'dir',
      lang === 'ar' ? 'rtl' : 'ltr'
    )
    document.documentElement.setAttribute('lang', lang)
  }

  const changeLanguage = (lang) => {
    locale.value = lang
    localStorage.setItem('language', lang)
    setHtmlAttributes(lang)
  }

  const toggleLanguage = () => {
    const languages = ['en', 'ar', 'de']
    const currentIndex = languages.indexOf(locale.value)
    const nextIndex = (currentIndex + 1) % languages.length
    changeLanguage(languages[nextIndex])
  }

  return {
    locale,
    changeLanguage,
    toggleLanguage
  }
}
