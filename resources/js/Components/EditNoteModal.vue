<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
        <div class="bg-gray-800 p-8 rounded-2xl w-full max-w-md shadow-lg text-white relative">
            <h2 class="text-2xl font-bold mb-6 text-center">Редактировать заметку</h2>

            <div class="mb-6 flex items-start space-x-4">
                <img
                    :src="note.book.cover_url"
                    alt="Обложка"
                    class="w-20 h-28 object-cover rounded-lg bg-gray-700"
                />
                <div class="flex-1">
                    <h3 class="text-xl font-bold">{{ note.book.title }}</h3>
                    <p class="text-gray-400">
                        {{ note.book.author || 'Автор не указан' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block mb-2 text-sm">Дата начала чтения</label>
                    <input
                        type="date"
                        v-model="startedAt"
                        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                </div>
                <div>
                    <label class="block mb-2 text-sm">Дата окончания чтения</label>
                    <input
                        type="date"
                        v-model="finishedAt"
                        :disabled="!startedAt"
                        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                    />
                </div>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm">Статус</label>
                <input
                    type="text"
                    :value="statusText"
                    readonly
                    class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 text-gray-400 focus:outline-none"
                />
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm">Заметка</label>
                <textarea
                    v-model="noteText"
                    rows="3"
                    class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                ></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <button @click="$emit('close')"
                        class="px-5 py-2 rounded-lg bg-gray-600 hover:bg-gray-500 transition text-sm">
                    Отменить
                </button>
                <button @click="updateNote"
                        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition text-sm font-semibold">
                    Сохранить
                </button>
            </div>

            <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                ✖
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "EditNoteModal",
    props: {
        note: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            startedAt: this.note.started_at || '',
            finishedAt: this.note.finished_at || '',
            noteText: this.note.note || '',
        }
    },
    computed: {
        status() {
            if (!this.startedAt) return 0;
            if (this.startedAt && !this.finishedAt) return 1;
            return 2;
        },
        statusText() {
            return ['Хочу прочитать', 'Читаю', 'Закончил'][this.status];
        }
    },
    methods: {
        async updateNote() {
            await axios.put(`/notes/${this.note.id}`, {
                started_at: this.startedAt || null,
                finished_at: this.finishedAt || null,
                note: this.noteText,
                status: this.status,
            });
            window.location.reload();
        }
    }
}
</script>
