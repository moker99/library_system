<template>
    <div class="p-6">
        <!-- 書櫃名稱 -->
        <h1 class="text-3xl font-bold mb-4">{{ shelfName }}</h1>

        <!-- 最外層操作按鈕 -->
        <div class="flex gap-2 mb-6">
        <button
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            @click="openAddBookModal(null)"
        >
            新增書本最佳位置
        </button>
        </div>

        <!-- 每層樓書籍清單 -->
        <div
        v-for="(layer, index) in shelves"
        :key="layer.id"
        class="mb-6"
        >
            <div class="text-lg font-semibold mb-2">
                第 {{ index + 1 }} 層（{{ layer.books.length }} / {{ layer.capacity }} 本）
            </div>

            <div class="flex flex-wrap gap-4">
                <!-- 書本卡片 -->
                <div
                v-for="(book, bIndex) in layer.books"
                :key="bIndex"
                class="w-48 p-4 rounded-lg shadow-md transition-transform transform hover:scale-105"
                :class="book.color"
                >
                <h3 class="font-bold text-lg mb-1">{{ book.title }}</h3>
                <!-- <p class="text-sm text-gray-700">{{ book.isbn }}</p> -->
                </div>

                <!-- 新增書本卡片 -->
                <div
                v-if="layer.books.length < layer.capacity"
                class="w-48 h-28 flex items-center justify-center border border-dashed border-gray-300 text-gray-500 rounded-lg cursor-pointer hover:bg-gray-50"
                @click="openAddBookModal(index)"
                >
                + 新增書本
                </div>
            </div>
        </div>

        <BookModal
        :visible="showModal"
        :form="form"
        :onSubmit="submitBook"
        :onCancel="closeModal"
        title="新增書本"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import BookModal from '@/components/BookModal.vue'

const route = useRoute()
const shelfId = route.params.id
const { proxy: globalProperties } = getCurrentInstance()

const shelfName = ref('')
const shelves = ref([])

const showModal = ref(false)
const activeLayerIndex = ref(null)

const form = ref({
    title: '',
    isbn: '',
})

const tailwindBgColors = [
    'bg-red-100', 'bg-orange-100', 'bg-yellow-100', 'bg-green-100',
    'bg-teal-100', 'bg-blue-100', 'bg-indigo-100', 'bg-purple-100', 'bg-pink-100',
]

function getRandomColor() {
    const index = Math.floor(Math.random() * tailwindBgColors.length)
    return tailwindBgColors[index]
}

async function loadShelfData() {
    try {
        const { data } = await axios.get(`/api/shelves/${shelfId}`)
        shelfName.value = data.name
        shelves.value = data.layers.map(layer => ({
            ...layer,
            books: layer.books.map(book => ({
                ...book,
                color: getRandomColor(),
            })),
        }))
    } catch (error) {
        console.error('讀取書櫃失敗', error)
    }
}

function openAddBookModal(layerIdx) {
    activeLayerIndex.value = layerIdx
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    form.value = { title: '', isbn: '' }
}

async function submitBook() {
    try {
        const payload = { ...form.value }

        if (activeLayerIndex.value === null) {
        await axios.post(`/api/shelves/${shelfId}/books`, payload)
        } else {
        const layerId = shelves.value[activeLayerIndex.value].id
        await axios.post(`/api/shelves/${shelfId}/layers/${layerId}/books`, payload)
        }

        closeModal()
        await loadShelfData()

        globalProperties?.$swal?.fire({
        title: '📘 書本新增成功',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        })
    } catch (error) {
        console.error('新增書本失敗', error)
        globalProperties?.$swal?.fire({
        title: '❌ 新增書本失敗',
        text: error?.response?.data?.error || '請稍後再試',
        icon: 'error',
        })
    }
}

onMounted(loadShelfData)
</script>

<style scoped>
</style>
