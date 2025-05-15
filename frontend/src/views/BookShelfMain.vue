<template>
	<div class="p-6">
		<!-- Header -->
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-3xl font-bold">我的書櫃</h1>
			<div class="flex gap-2">
				<button
					class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
					@click="showModal = true"
				>
					+ 新增書櫃
				</button>
				<button
					class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
					@click="showBookModal = true"
				>
					+ 新增書本至最佳位置
				</button>
			</div>
		</div>

		<!-- 書櫃清單 -->
		<!-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
			<div
				v-for="(shelf, index) in shelves"
				:key="index"
				class="p-4 bg-white rounded-2xl shadow-md hover:shadow-xl transition-transform transform hover:scale-105 cursor-pointer"
				@click="goToShelf(shelf.id)"
			>
				<h2 class="text-xl font-semibold mb-1">{{ shelf.name }}</h2>
				<p class="text-sm text-gray-600 mb-2">{{ shelf.location }}</p>
				<div class="text-sm text-gray-800">樓層：{{ shelf.layer_count }}</div>
				<div class="text-sm text-gray-800">容量：{{ shelf.total_books }}/{{ shelf.capacity }}</div>
			</div>
		</div> -->
		<draggable
			v-model="shelves"
			group="shelves"
			item-key="id"
			class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
			@end="onDragEnd"
		>
			<template #item="{ element }">
				<div
					class="p-4 bg-white rounded-2xl shadow-md hover:shadow-xl transition-transform transform hover:scale-105 cursor-pointer"
					@click="goToShelf(element.id)"
				>
					<h2 class="text-xl font-semibold mb-1">{{ element.name }}</h2>
					<p class="text-sm text-gray-600 mb-2">{{ element.location }}</p>
					<div class="text-sm text-gray-800">樓層：{{ element.layer_count }}</div>
					<div class="text-sm text-gray-800">容量：{{ element.total_books }}/{{ element.capacity }}</div>
				</div>
			</template>
		</draggable>

		<!-- 新增書櫃 Modal -->
		<div
			v-if="showModal"
			class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
		>
			<div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
				<h2 class="text-xl font-bold text-center mb-4">新增書櫃</h2>

				<form @submit.prevent="submitForm" class="space-y-4">
					<!-- 書櫃名稱 -->
					<div>
						<label class="block text-sm font-medium text-gray-700">書櫃名稱</label>
						<input v-model="form.name" type="text" class="w-full mt-1 px-3 py-2 border rounded" />
					</div>

					<!-- 書櫃簡稱 -->
					<div>
						<label class="block text-sm font-medium text-gray-700">書櫃簡稱</label>
						<input v-model="form.location" type="text" class="w-full mt-1 px-3 py-2 border rounded" />
					</div>

					<!-- 層數 -->
					<div>
						<label class="block text-sm font-medium text-gray-700">層數</label>
						<input v-model.number="form.layer_count" type="number" class="w-full mt-1 px-3 py-2 border rounded" />
					</div>

					<!-- 每層最大書籍數 -->
					<div>
						<label class="block text-sm font-medium text-gray-700">每層最大書籍數</label>
						<input v-model.number="form.capacity_per_layer" type="number" class="w-full mt-1 px-3 py-2 border rounded" />
					</div>
				</form>

				<!-- 按鈕 -->
				<div class="flex justify-end gap-3 mt-4">
					<button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">取消</button>
					<button type="button" @click="submitForm" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">確認</button>
				</div>
			</div>
		</div>

		<BookModal
			:visible="showBookModal"
			:form="form"
			:onSubmit="submitBook"
			:onCancel="closeBookModal"
			title="新增書本"
        />
	</div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import draggable from 'vuedraggable'
import BookModal from '@/components/BookModal.vue'

const { proxy: globalProperties } = getCurrentInstance()
const router = useRouter()
const shelves = ref([])
const showModal = ref(false)
const showBookModal = ref(false)

const form = ref({
	name: '',
	location: '',
	layer_count: 1,
	capacity_per_layer: 1,
})

const bookForm = ref({
	title: '',
	isbn: '',
})

const loadShelves = async () => {
	const { data } = await axios.get('/api/shelves')
	shelves.value = data
}

async function onDragEnd() {
	const payload = shelves.value.map((shelf, index) => ({
		id: shelf.id,
		sort_order: index
	}))
	await axios.post('/api/shelves/reorder', { shelves: payload })
	await loadShelves()
}

const submitForm = async () => {
	const payload = {
		...form.value,
		capacity: form.value.layer_count * form.value.capacity_per_layer,
		total_books: 0,
	}

	try {
		await axios.post('/api/shelves', payload)
		await loadShelves()
		closeModal()
		globalProperties.$swal.fire({
			title: '📁 書櫃新增成功！',
			icon: 'success',
			timer: 2000,
			showConfirmButton: false,
		})
	} catch (err) {
		globalProperties.$swal.fire({
			title: '新增書櫃失敗',
			icon: 'error'
		})
		console.error(err)
	}
}

const submitBook = async () => {
	try {
		const payload = { ...form.value }
		const { data } = await axios.post('/api/books/auto', payload)
		await loadShelves()
		closeBookModal()
		globalProperties.$swal.fire({
			title: '📚 書本新增成功！',
			html: `已放入 <b>${data.shelf}</b> 的第 <b>${data.layer}</b> 層，第 <b>${data.position}</b> 格`,
			icon: 'success',
			timer: 2000,
			showConfirmButton: false,
		})
	} catch (err) {
		globalProperties.$swal.fire({
			title: '❌ 新增書本失敗',
			text: err.response?.data?.error || '請稍後再試',
			icon: 'error'
		})
		console.error(err)
	}
}

function goToShelf(id) {
	router.push(`/shelves/${id}`)
}

function closeModal() {
	showModal.value = false
	form.value = {
		name: '',
		location: '',
		layer_count: 1,
		capacity_per_layer: 1,
	}
}

function closeBookModal() {
	showBookModal.value = false
	bookForm.value = {
		title: '',
		isbn: '',
	}
}

onMounted(loadShelves)
</script>

<style scoped>
</style>