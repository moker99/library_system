// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

import BookSearchView from '../views/BookSearchView.vue'
import BookShelfPage from '../views/BookShelfPage.vue'
import BookShelfMain from '../views/BookShelfMain.vue'

const routes = [
    { path: '/', redirect: '/shelves' },
    { path: '/shelves', component: BookShelfMain },
    { path: '/shelves/:id', component: BookShelfPage },
    { path: '/books/search', component: BookSearchView },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
