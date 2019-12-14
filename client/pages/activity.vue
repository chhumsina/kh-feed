<template>
  <div>

    <no-ssr>
      <save-post-item/>
    </no-ssr>

  </div>
</template>

<script>

    import SavePostItem from '../components/activity-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            SavePostItem
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
