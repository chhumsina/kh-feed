<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white top-nav">
    <div class="container">
      <div class="has-search" style="width: 100%">
        <span class="fa fa-search form-control-feedback"></span>
        <input
          type="text"
          class="form-control"
          placeholder="Search Post, People"
        />
      </div>
    </div>
  </nav>
</template>

<style scoped>
.top-nav {
  font-weight: 600;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
  position: fixed;
  width: 100%;
  z-index: 2;
  top: 0;
}
.has-search .form-control {
  background: #fafafa;
  padding-left: 2.375rem;
  border: 1px solid #efefef;
  border-radius: 20px;
  width: 100%;
}

.has-search .form-control-feedback {
  position: absolute;
  z-index: 2;
  display: block;
  width: 2.375rem;
  height: 2.375rem;
  line-height: 2.375rem;
  text-align: center;
  pointer-events: none;
  color: #aaa;
}
</style>
