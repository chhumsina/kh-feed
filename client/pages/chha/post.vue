<template>
  <div>

    <no-ssr>
      <chha-post/>
    </no-ssr>

  </div>
</template>

<script>

    import ChhaPost from '../../components/chha/chha-post'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
          ChhaPost
        }
    }
</script>

<style scopped>


</style>
