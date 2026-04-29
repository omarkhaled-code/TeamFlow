<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
    const token = new URLSearchParams(window.location.search).get('token')

    if (token) {
        localStorage.setItem('token', token)
        router.push('/dashboard')
    }
})
</script>

<template>
    <p>Logging you in...</p>
</template>
