<template>
  <div>

    <no-ssr>
      <activity-item/>
    </no-ssr>

  </div>
</template>

<script>

    import ActivityItem from '../components/activity-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            ActivityItem
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
