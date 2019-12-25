<template>
  <div style="margin-top: 67px;">

    <no-ssr>
      <book-post-item/>
    </no-ssr>

  </div>
</template>

<script>

    import BookPostItem from '../components/book-post-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
          BookPostItem
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
