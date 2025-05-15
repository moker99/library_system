<template>
    <div class="p-8 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">書本查詢與定位</h1>
    
        <!-- 查詢輸入區 -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">請輸入書名或 ISBN（最多 3 筆）</label>
            <div class="space-y-2 mb-4">
            <input
                v-for="(term, index) in searchTerms"
                :key="index"
                v-model="searchTerms[index]"
                type="text"
                placeholder="書名或 ISBN..."
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            </div>
            <div class="flex justify-between items-center mt-4">
                <button
                    @click="addInput"
                    :disabled="searchTerms.length >= 3"
                    class="text-sm text-blue-600 hover:underline disabled:text-gray-400"
                >
                    + 新增欄位
                </button>
                <button
                    @click="searchBooks"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    查詢
                </button>
            </div>
        </div>
    
        <!-- 查詢結果 -->
        <div v-if="results.length" class="space-y-4">
            <h2 class="text-xl font-semibold mb-2">查詢結果（依最佳取書順序排列）</h2>
            <ul class="space-y-3">
                <li
                v-for="(book, index) in results"
                :key="book.id"
                class="flex gap-4 bg-gray-50 p-4 rounded shadow-sm border-l-4 border-blue-500"
                >
                    <!-- 書本圖片 -->
                    <img
                        src="/images/I_upgraded_alone.png"
                        alt="封面"
                        class="w-16 h-24 object-cover rounded border"
                    />

                    <!-- 書籍資訊 -->
                    <div>
                        <div class="font-semibold text-blue-800">{{ index + 1 }}. {{ book.title }}</div>
                        <div class="text-sm text-gray-700">ISBN：{{ book.isbn }}</div>
                        <div class="text-sm text-gray-700">
                        書櫃：位置 {{ book.shelf_sort_order }} ｜ {{ book.shelf_name }} ｜ 第 {{ book.layer_level }} 層 ｜ 位於第 {{ book.position }} 格
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    
        <div v-else-if="searched" class="text-center text-gray-500 mt-8">查無資料，請確認輸入。</div>
    </div>
</template>

<script setup>
    import { ref } from 'vue'
    import axios from 'axios'

    const searchTerms = ref([''])
    const results = ref([])
    const searched = ref(false)

    function addInput() {
    if (searchTerms.value.length < 3) {
        searchTerms.value.push('')
    }
    }

    async function searchBooks() {
        try {
            const query = searchTerms.value.filter(t => t.trim() !== '')
            if (!query.length) return

            const { data } = await axios.post('/api/books/batch-search', { keywords: query })

            results.value = data.results || []
            searched.value = true
        } catch (error) {
            console.error('查詢失敗', error)
            results.value = []
            searched.value = true
        }
    }
</script>

<style scoped>
</style>
