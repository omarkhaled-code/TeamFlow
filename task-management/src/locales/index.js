import { createI18n } from 'vue-i18n'
import en from './en.json'
import ar from './ar.json'
import ge from './ge.json'

const i18n = createI18n({
  legacy: false, // استخدام Composition API
  locale: localStorage.getItem('language') || 'ge', // اللغة الافتراضية
  fallbackLocale: 'en',
  messages: {
    en,
    ar,
    ge
  }
})

export default i18n