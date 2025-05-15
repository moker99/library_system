<script setup>
</script>

<template>

  <div class="p-4">
    <nav class="mb-4 space-x-4">
      <router-link to="/shelves">📁 書櫃管理</router-link>
      <router-link to="/books/search">📘 查詢書籍</router-link>
      <!-- <router-link to="/books/add">➕ 新增書籍</router-link> -->
    </nav>
    <router-view />
  </div>
</template>

<style scoped>
header {
  background-color: #f4f4f4;
  text-align: center;
  padding: 1rem 0;
  border-bottom: 1px solid #ccc;
  position: sticky;
  top: 0;
  z-index: 1000;
}

nav {
  margin-top: 0.5rem;
}

nav a {
  margin: 0 1rem;
  text-decoration: none;
  color: #42b983;
  font-weight: bold;
}

nav a.router-link-active {
  color: #35495e;
  text-decoration: underline;
}

main {
  padding: 2rem;
}
</style>
