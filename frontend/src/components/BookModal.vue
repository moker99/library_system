<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-bold text-center mb-4">{{ title }}</h2>
            <form @submit.prevent="onSubmit" class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">書名</label>
                <input v-model="form.title" type="text" class="w-full mt-1 px-3 py-2 border rounded" required />
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">ISBN</label>
                <input v-model="form.isbn" type="text" class="w-full mt-1 px-3 py-2 border rounded" required />
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="onCancel" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">取消</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">確認</button>
            </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        visible: Boolean,
        title: { type: String, default: '新增書本' },
        form: Object,
        onSubmit: Function,
        onCancel: Function,
    })
</script>
