<template>
  <div style="margin-top: 67px;">

    <no-ssr>
      <post-item/>
    </no-ssr>

  </div>
</template>

<script>

    import PostItem from '../components/post-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
          PostItem
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
