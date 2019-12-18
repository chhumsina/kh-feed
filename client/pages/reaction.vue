<template>
  <div>

    <no-ssr>
      <reaction-item/>
    </no-ssr>

  </div>
</template>

<script>

    import ReactionItem from '../components/reaction-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            ReactionItem
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
