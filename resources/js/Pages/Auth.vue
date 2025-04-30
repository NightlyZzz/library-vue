<template>
    <Layout class="bg-gray-900">
        <div class="flex flex-col justify-center items-center min-h-[80vh] p-6">
            <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-3xl shadow-2xl">
                <h2 class="text-center text-4xl font-extrabold text-white mb-6">
                    {{ isLogin ? 'Вход' : 'Регистрация' }}
                </h2>

                <form @submit.prevent="handleSubmit" class="space-y-5">
                    <div v-if="!isLogin" class="relative">
                        <div class="input-wrapper flex items-center h-12 relative">
                            <input
                                v-model="form.nickname"
                                type="text"
                                placeholder="Никнейм"
                                :class="['input', errors.nickname ? 'input-error' : '']"
                            />
                            <span class="input-icon">👤</span>
                        </div>
                        <small v-if="errors.nickname" class="error-text">{{ errors.nickname }}</small>
                    </div>

                    <div class="relative">
                        <div class="input-wrapper flex items-center h-12 relative">
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Почта"
                                :class="['input', errors.email ? 'input-error' : '']"
                            />
                            <span class="input-icon">✉️</span>
                        </div>
                        <small v-if="errors.email" class="error-text">{{ errors.email }}</small>
                    </div>

                    <div class="relative">
                        <div class="input-wrapper flex items-center h-12 relative">
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Пароль"
                                :class="['input', errors.password ? 'input-error' : '']"
                            />
                            <span class="input-icon">🔒</span>
                        </div>
                        <small v-if="errors.password" class="error-text">{{ errors.password }}</small>
                    </div>

                    <div v-if="!isLogin" class="relative">
                        <div class="input-wrapper flex items-center h-12 relative">
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Повторите пароль"
                                :class="['input', errors.password_confirmation ? 'input-error' : '']"
                            />
                            <span class="input-icon">🔒</span>
                        </div>
                        <small v-if="errors.password_confirmation" class="error-text">{{ errors.password_confirmation }}</small>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input v-model="form.remember" type="checkbox" id="remember" class="custom-checkbox" />
                        <label for="remember" class="text-gray-300 text-sm select-none">
                            Запомнить меня
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:scale-105 transform transition font-semibold shadow-md flex justify-center items-center active:scale-95 active:bg-indigo-800">
                        <span v-if="!loading">{{ isLogin ? 'Войти' : 'Зарегистрироваться' }}</span>
                        <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>
                </form>

                <div class="flex flex-col space-y-2 mt-6 text-center">
                    <button @click="toggleForm" class="text-indigo-500 hover:underline text-sm transition">
                        {{ isLogin ? 'Нет аккаунта? Зарегистрироваться' : 'Уже есть аккаунт? Войти' }}
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import axios from 'axios';
import Layout from '../Layouts/Layout.vue';

export default {
    components: {
        Layout
    },
    data() {
        return {
            isLogin: true,
            loading: false,
            form: {
                nickname: '',
                email: '',
                password: '',
                password_confirmation: '',
                remember: false,
            },
            errors: {}
        }
    },
    methods: {
        toggleForm() {
            this.isLogin = !this.isLogin;
            this.resetForm();
        },
        resetForm() {
            this.form = {
                nickname: '',
                email: '',
                password: '',
                password_confirmation: '',
                remember: false,
            }
            this.errors = {};
        },
        async handleSubmit() {
            this.loading = true;
            this.errors = {};

            try {
                if (this.isLogin) {
                    await axios.post('/login', {
                        email: this.form.email,
                        password: this.form.password,
                        remember: this.form.remember,
                    });
                } else {
                    await axios.post('/register', {
                        nickname: this.form.nickname,
                        email: this.form.email,
                        password: this.form.password,
                        password_confirmation: this.form.password_confirmation,
                    });
                }
                window.location.href = '/'; // редирект после входа
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const fieldTranslations = {
                        email: 'Почта',
                        password: 'Пароль',
                        nickname: 'Никнейм',
                        password_confirmation: 'Повторите пароль'
                    };
                    const phraseTranslations = {
                        'The email field is required.': 'Поле Почта обязательно для заполнения.',
                        'The password field is required.': 'Поле Пароль обязательно для заполнения.',
                        'The nickname field is required.': 'Поле Никнейм обязательно для заполнения.',
                        'The password confirmation does not match.': 'Подтверждение пароля не совпадает.',
                        'The password confirmation field is required.': 'Поле Повторите пароль обязательно для заполнения.',
                        'The email must be a valid email address.': 'Почта должна быть действительным адресом электронной почты.',
                        'The password must be at least 8 characters.': 'Пароль должен содержать не менее 8 символов.',
                        'The nickname must be at least 3 characters.': 'Никнейм должен содержать не менее 3 символов.',
                        // Добавьте другие фразы по необходимости
                    };
                    const serverErrors = error.response.data.errors;
                    const translatedErrors = {};
                    for (const key in serverErrors) {
                        if (serverErrors.hasOwnProperty(key)) {
                            translatedErrors[key] = serverErrors[key].map(msg => {
                                if (phraseTranslations[msg]) {
                                    return phraseTranslations[msg];
                                } else {
                                    // Если нет точного совпадения, заменяем поле в сообщении
                                    const translatedField = fieldTranslations[key] || key;
                                    return msg.replace(new RegExp(key, 'gi'), translatedField);
                                }
                            }).join(' ');
                        }
                    }
                    this.errors = translatedErrors;
                } else {
                    alert('Ошибка сервера');
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.input-wrapper {
    position: relative;
    height: 3rem; /* 12 * 0.25rem = 3rem */
    display: flex;
    align-items: center;
}

.input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #374151;
    border-radius: 0.75rem;
    background-color: #1f2937;
    color: white;
    font-size: 1rem;
    outline: none;
    transition: all 0.3s ease;
    position: relative;
}
.input:focus,
.input:hover {
    border-color: #6366f1;
    background-color: #111827;
    box-shadow: 0 0 0 2px #2563eb33;
}

/* Ошибки: выделение красным */
.input-error {
    border-color: #f87171 !important;
    box-shadow: 0 0 0 2px #f8717133 !important;
    background-color: #1f2937;
}
.input-error:focus,
.input-error:hover {
    border-color: #f87171 !important;
    box-shadow: 0 0 0 2px #f8717133 !important;
    background-color: #1f2937;
}
.input-icon {
    position: absolute;
    left: 0.75rem;
    font-size: 1.3rem;
    color: #cbd5e1; /* brighter color */
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
.error-text {
    color: #f87171;
    background-color: rgba(248,113,113,0.15); /* #f87171 с прозрачностью ~15% */
    font-size: 0.85rem;
    margin-top: 0.4rem;
    display: block;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 6px 0 rgba(248,113,113,0.08), 0 1.5px 4px 0 rgba(40,40,40,0.04);
    font-weight: 500;
    line-height: 1.3;
}

/* Custom checkbox styles */
.custom-checkbox {
    appearance: none;
    -webkit-appearance: none;
    background-color: #1f2937;
    border: 2px solid #4b5563;
    width: 20px;
    height: 20px;
    border-radius: 0.375rem;
    position: relative;
    cursor: pointer;
    transition: border-color 0.3s ease, background-color 0.3s ease;
    display: inline-block;
    vertical-align: middle;
}

.custom-checkbox:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
}

.custom-checkbox:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}

.custom-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    animation: checkmark 0.3s ease forwards;
}

@keyframes checkmark {
    0% {
        width: 0;
        height: 0;
        opacity: 0;
    }
    50% {
        width: 6px;
        height: 0;
        opacity: 1;
    }
    100% {
        width: 6px;
        height: 12px;
        opacity: 1;
    }
}

label[for="remember"] {
    line-height: 1;
    user-select: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    color: #d1d5db;
}

/* Align checkbox and label text vertically center */
.flex.items-center.space-x-3 {
    align-items: center;
}

/* Button active state animation */
button[type="submit"] {
    transition: background-color 0.3s ease, transform 0.1s ease;
}
button[type="submit"]:active {
    transform: scale(0.95);
    background-color: #4f46e5;
}
</style>
