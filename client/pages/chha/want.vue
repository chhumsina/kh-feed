<template>
  <div>

    <no-ssr>
      <chha-want/>
    </no-ssr>

  </div>
</template>

<script>

    import ChhaWant from '../../components/chha/chha-want'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
          ChhaWant
        }
    }
</script>

<style scopped>


</style>
