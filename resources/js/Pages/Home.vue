<template>
    <Layout>
        <div class="flex flex-col items-center min-h-[80vh] p-6 text-white space-y-6">

            <div class="w-full max-w-3xl flex justify-between items-center">
                <button
                    @click="showCreate = true"
                    class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition text-white font-semibold active:scale-95"
                >
                    ➕ Добавить заметку
                </button>
            </div>

            <div v-if="notes.length" class="w-full max-w-3xl space-y-4">
                <div
                    v-for="note in notes"
                    :key="note.id"
                    @click="openEdit(note)"
                    class="bg-gray-800 p-6 rounded-2xl shadow-md flex space-x-4 cursor-pointer hover:bg-gray-700 transition"
                >
                    <img
                        :src="note.book.cover_url"
                        alt="Обложка"
                        class="w-20 h-28 object-cover rounded-lg bg-gray-700"
                    />

                    <div class="flex-1">
                        <h2 class="text-xl font-bold">{{ note.book.title }}</h2>
                        <p class="text-gray-400">{{ note.book.author }}</p>
                        <p class="mt-2 text-sm text-gray-300">{{ note.note || '— без заметки —' }}</p>
                    </div>

                    <div class="flex flex-col items-end space-y-1 text-sm text-gray-400">
                        <div>Статус: <span class="text-white">{{ statusText(note.status) }}</span></div>
                        <div>Начало: {{ formatDate(note.started_at) }}</div>
                        <div>Окончание: {{ formatDate(note.finished_at) }}</div>
                    </div>
                </div>
            </div>
            <p v-else class="text-gray-400">У вас ещё нет заметок. Нажмите «Добавить заметку» выше.</p>

            <CreateNoteModal v-if="showCreate" @close="showCreate = false" />
            <EditNoteModal   v-if="showEdit"   :note="editingNote" @close="showEdit = false" />
        </div>
    </Layout>
</template>

<script>
import Layout from '../Layouts/Layout.vue'
import CreateNoteModal from '../Components/CreateNoteModal.vue'
import EditNoteModal   from '../Components/EditNoteModal.vue'

export default {
    components: {
        Layout,
        CreateNoteModal,
        EditNoteModal
    },
    props: {
        notes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            showCreate: false,
            showEdit: false,
            editingNote: null
        }
    },
    methods: {
        openEdit(note) {
            this.editingNote = note
            this.showEdit = true
        },
        statusText(status) {
            return ['Хочу прочитать', 'Читаю', 'Закончил'][status]
        },
        formatDate(date) {
            return date ? new Date(date).toLocaleDateString('ru-RU') : '—'
        }
    }
}
</script>
