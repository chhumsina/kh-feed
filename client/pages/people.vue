<template>
  <div style="margin-top: 67px;">

    <no-ssr>
      <people-item/>
    </no-ssr>

  </div>
</template>

<script>

    import PeopleItem from '../components/people-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
          PeopleItem
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
