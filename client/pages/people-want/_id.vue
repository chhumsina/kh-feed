<template>
  <div>

    <no-ssr>
      <people-want-item/>
    </no-ssr>

  </div>
</template>

<script>

    import PeopleWantItem from '../../components/people-want-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
            }
        },
        components: {
            PeopleWantItem
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
