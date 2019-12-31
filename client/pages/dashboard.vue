<template>
  <div style="margin-top: 67px;">

    <no-ssr>
      <dashboard-item/>
    </no-ssr>

  </div>
</template>

<script>

    import DashboardItem from '../components/dashboard-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            DashboardItem
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
