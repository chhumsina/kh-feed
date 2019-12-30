<template>
  <div style="margin-top: 67px;">

    <no-ssr>
      <ineed-item/>
    </no-ssr>

  </div>
</template>

<script>

    import IneedItem from '../components/ineed-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            IneedItem
        },
        mounted() {
        },
        methods: {

            async logout() {
                await this.$auth.logout()
            }
        }
    }
</script>

<style scopped>


</style>
