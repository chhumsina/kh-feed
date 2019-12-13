<template>
  <div>
    <p>Not yet bro</p>
  </div>
</template>

<script>
export default {
  middleware: 'auth',
  data() {
    return {
      strategy: this.$auth.$storage.getUniversal('strategy')
    }
  },
  mounted() {},
  methods: {
    async logout() {
      await this.$auth.logout()
    }
  }
}
</script>
