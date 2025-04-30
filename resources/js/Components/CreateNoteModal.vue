<template>
    <div>
        <transition name="fade">
            <div
                v-if="showErrorToast"
                class="fixed top-4 right-4 z-60 bg-red-600 border border-red-700 text-white px-4 py-2 rounded shadow-md"
            >
                {{ errorToastMessage }}
            </div>
        </transition>

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
            <div class="bg-gray-800 p-8 rounded-2xl w-full max-w-2xl shadow-lg text-white relative">
                <h2 class="text-2xl font-bold mb-6 text-center">Добавить заметку</h2>

                <div class="mb-6">
                    <label class="block mb-2 text-sm">Поиск книги</label>
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="searchBooks"
                        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Введите название книги..."
                    />
                </div>

                <div v-if="searchResults.length" class="mb-6 max-h-40 overflow-y-auto space-y-2">
                    <div
                        v-for="book in searchResults"
                        :key="book.id"
                        @click="selectBook(book)"
                        class="p-3 rounded-lg bg-gray-700 hover:bg-gray-600 cursor-pointer transition"
                    >
                        <div class="font-semibold">{{ book.volumeInfo.title }}</div>
                        <div class="text-gray-400 text-sm">
                            {{ book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Автор не указан' }}
                        </div>
                    </div>
                </div>

                <div v-if="selectedBook">
                    <div class="mb-6 flex items-start space-x-4">
                        <img
                            :src="selectedBook.volumeInfo.imageLinks?.thumbnail"
                            alt="Обложка"
                            class="w-24 h-32 object-cover rounded-lg bg-gray-700"
                        />
                        <div class="flex-1">
                            <h3 class="text-xl font-bold">{{ selectedBook.volumeInfo.title }}</h3>
                            <p class="text-gray-400">
                                {{ selectedBook.volumeInfo.authors ? selectedBook.volumeInfo.authors.join(', ') : 'Автор не указан' }}
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
                            v-model="note"
                            rows="3"
                            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Ваши мысли о книге..."
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button @click="$emit('close')"
                                class="px-5 py-2 rounded-lg bg-gray-600 hover:bg-gray-500 transition text-sm">
                            Отменить
                        </button>
                        <button @click="saveNote"
                                class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition text-sm font-semibold">
                            Сохранить
                        </button>
                    </div>
                </div>

                <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                    ✖
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "CreateNoteModal",
    data() {
        return {
            searchQuery: '',
            searchResults: [],
            selectedBook: null,
            startedAt: '',
            finishedAt: '',
            note: '',
            showErrorToast: false,
            errorToastMessage: ''
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
        async searchBooks() {
            if (this.searchQuery.length < 3) {
                this.searchResults = [];
                return;
            }
            try {
                const resp = await axios.get(
                    `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(this.searchQuery)}&key=AIzaSyC-O7g42_RfOip_InX_2lJB6Ptmv6wRszY`
                );
                this.searchResults = resp.data.items || [];
            } catch (e) {
                console.error('Ошибка поиска книг:', e);
            }
        },
        selectBook(book) {
            this.selectedBook = book;
            this.searchQuery = '';
            this.searchResults = [];
            this.startedAt = '';
            this.finishedAt = '';
            this.note = '';
        },
        async saveNote() {
            if (!this.selectedBook) {
                alert('Выберите книгу!');
                return;
            }
            try {
                await axios.post('/notes', {
                    book: {
                        google_id: this.selectedBook.id,
                        title: this.selectedBook.volumeInfo.title,
                        author: this.selectedBook.volumeInfo.authors
                            ? this.selectedBook.volumeInfo.authors.join(', ')
                            : null,
                        cover_url: this.selectedBook.volumeInfo.imageLinks?.thumbnail || null,
                    },
                    note: {
                        started_at: this.startedAt,
                        finished_at: this.finishedAt,
                        note: this.note,
                        status: this.status,
                    }
                });
                window.location.reload();
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const messages = error.response.data.errors.note || ['Ошибка при сохранении'];
                    this.errorToastMessage = messages.join(' ');
                    this.showErrorToast = true;
                    setTimeout(() => {
                        this.showErrorToast = false;
                    }, 4000);
                } else {
                    console.error(error);
                }
            }
        }
    }
}
</script>

<style scoped>
/* Переход для toast */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
