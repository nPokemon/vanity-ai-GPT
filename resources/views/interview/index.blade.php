<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta charset="UTF-8"><link rel="icon" href="/favicon.png">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <main id="app">
        <section class="chat">
            <div class="chat--title"><h1>Vanity AI</h1></div>
            <div :class="['chat--preview', 'start', !isStarted && !isFinished && !isSubmitted ? '' : 'hidden']">
                <div class="chat--preview__description">
                    <img
                    src="/images/hunter_start.png"
                    class="chat--preview__description--img"
                    />
                    <p class="chat--preview__description--text">Welcome, @{{ intervieweeName }}</p>
                    <p class="chat--preview__description--text">
                        You're in the <b>Vanity AI</b> - AI Chat-Based Interview Room.
                    </p>
                    <p class="chat--preview__description--text">
                        <u>Here's what to expect:</u>
                    </p>
                    <p class="chat--preview__description--text">
                        <b>Interview Process:</b> Our AI will chat with you about your
                        skills and experiences for about 30 minutes.
                    </p>
                    <p class="chat--preview__description--text">
                        <b>Text Responses:</b> Please provide clear and concise answers.
                    </p>
                </div>
                <div class="chat--preview__action">
                    <p class="chat--preview__action--text">
                        Ready? Click 'Start Interview' below.
                    </p>
                    <a class="chat--preview__action--button" @click.prevent="startInterview">Start Interview</a>
                </div>
            </div>
            <div :class="['chat--main', (isStarted || isFinished) && !isSubmitted ? '' : 'hidden', isFinished ? 'review' : '']">
                <div class="chat--main__messages" ref="messagesContainer">
                    <template v-for="message in messages">
                        <div v-if="message.role == MessageRoles.ASSISTANT" class="chat--main__messages--ai">
                            <img
                            src="/images/hunter_icon.png"
                            class="chat--main__messages--ai__photo"
                            />
                            <p class="chat--main__messages--ai__text">@{{ message.content }}</p>
                        </div>
                        <div v-if="message.role == MessageRoles.USER" :class="['chat--main__messages--user', isFinished ? 'warning' : '']">
                            <img
                            src="/images/user_icon.png"
                            class="chat--main__messages--user__photo"
                            />
                            <p class="chat--main__messages--user__text">@{{ message.content }}</p>
                            <div class="chat--main__messages--user__actions">
                                <button @click.prevent="openDeleteConfirmation(message.id)" type="button" :class="['chat--main__messages--user__actions--button remove', messagesWithOpenDeletionConfirmation[message.id] || !isFinished ? 'hidden' : '']">
                                    <svg class="chat--main__messages--user__actions--button--icon">
                                        <use xlink:href="#remove"></use>
                                    </svg>
                                </button>
                                <button @click.prevent="confirmDeletion(message.id)" type="button" :class="['chat--main__messages--user__actions--button tick', messagesWithOpenDeletionConfirmation[message.id] ? '' : 'hidden']">
                                    <svg class="chat--main__messages--user__actions--button--icon">
                                        <use xlink:href="#tick"></use>
                                    </svg>
                                </button>
                                <button @click.prevent="cancelDeletion(message.id)" type="button" :class="['chat--main__messages--user__actions--button cross', messagesWithOpenDeletionConfirmation[message.id] ? '' : 'hidden']">
                                    <svg class="chat--main__messages--user__actions--button--icon">
                                        <use xlink:href="#cross"></use>
                                    </svg>
                                </button>
                                <p class="chat--main__messages--user__actions--warning hidden">Are you sure you want delete this answer?</p>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="chat--main__input">
                    <form action="" class="chat--main__input--form">
                        <textarea
                            placeholder="Enter your request"
                            name="search"
                            class="chat--main__input--form__input"
                            v-model="userMessageContent"
                        ></textarea>
                        <button @click.prevent="sendMessage" type="submit" :class="['chat--main__input--form__button', showLoader ? 'hidden' : '']">
                            <svg class="chat--main__input--form__button--icon">
                                <use xlink:href="#send-message"></use>
                            </svg>
                        </button>
                        <div role="status" :class="['chat--main__input--form__loader', showLoader ? '' : 'hidden']">
                            <svg
                                aria-hidden="true"
                                class="chat--main__input--form__loader--icon"
                                viewBox="0 0 100 101"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                >
                                <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                                />
                                <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="black"
                                />
                            </svg>
                        </div>
                    </form>
                </div>
                <div :class="['chat--main__action', isFinished ? 'hidden' : '']">
                    <div class="chat--main__action--buttons">
                        <a @click.prevent="skipQuestion" class="chat--main__action--buttons__item white"
                        >Skip Question</a
                        >
                        <a @click.prevent="endInterview" class="chat--main__action--buttons__item black"
                        >End Interview</a
                        >
                    </div>
                    <p class="chat--main__action--text">
                        You can finish interview by click this button
                    </p>
                </div>
                <div :class="['chat--main__action', isFinished ? '' : 'hidden']">
                    <p class="chat--main__action--text">
                        As part of our commitment to ensuring a fair evaluation, you have
                        the opportunity to review the text of your interview and make any
                        corrections if necessary. You can send your interview by click
                        this button.
                    </p>
                    <div class="chat--main__action--buttons">
                        <a @click.prevent="submitInterview" class="chat--main__action--buttons__item black"
                        >Submit Interview</a
                        >
                    </div>
                </div>
            </div>
            </div>

            <div :class="['chat--preview', 'end', isSubmitted ? '' : 'hidden']">
                <div class="chat--preview__description">
                    <img
                    src="/images/hunter_end.png"
                    class="chat--preview__description--img"
                    />

                    <p class="chat--preview__description--text">
                        Thank You For Participating!
                    </p>
                    <p class="chat--preview__description--text">
                        Congratulations on completing your interview with <b>Vanity AI</b>
                    </p>
                    <p class="chat--preview__description--text"><u>What's Next?</u></p>
                    <p class="chat--preview__description--text">
                        Our team will carefully review your responses. We understand the
                        anticipation and assure you that we will be in touch soon. Expect
                        to hear from us within 1-2 days.
                    </p>
                </div>
                <div class="chat--preview__action">
                    <p class="chat--preview__action--text">
                        You can return to home page by clicking this button
                    </p>
                    <a href="/" class="chat--preview__action--button">Home Page</a>
                </div>
            </div>
        </section>
    </main>
    <div class="defs w-20 bg-gray-300 mx-auto" hidden>
        <svg
            id="send-message"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            >
            <path
            d="M19.7157 3.36563L2.24068 8.2875C2.09193 8.32817 1.95932 8.41371 1.86094 8.53246C1.76255 8.65122 1.70316 8.79742 1.69087 8.95114C1.67857 9.10486 1.71396 9.25864 1.79221 9.39153C1.87047 9.52442 1.98779 9.62995 2.12818 9.69375L10.1532 13.4906C10.3104 13.5634 10.4366 13.6897 10.5094 13.8469L14.3063 21.8719C14.3701 22.0123 14.4756 22.1296 14.6085 22.2078C14.7414 22.2861 14.8952 22.3215 15.0489 22.3092C15.2026 22.2969 15.3488 22.2375 15.4676 22.1391C15.5864 22.0407 15.6719 21.9081 15.7126 21.7594L20.6344 4.28438C20.6717 4.15685 20.674 4.02165 20.6411 3.89293C20.6082 3.76421 20.5412 3.64672 20.4473 3.55277C20.3533 3.45882 20.2359 3.39188 20.1071 3.35895C19.9784 3.32602 19.8432 3.32833 19.7157 3.36563V3.36563Z"
            stroke="#797979"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            />
            <path
            d="M10.3965 13.6031L14.634 9.36562"
            stroke="#797979"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            />
        </svg>
        <svg
            id="remove"
            viewBox="0 0 24 25"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M3 6.86206H5H21"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <path
                d="M8 6.86206V4.86206C8 4.33163 8.21071 3.82292 8.58579 3.44785C8.96086 3.07277 9.46957 2.86206 10 2.86206H14C14.5304 2.86206 15.0391 3.07277 15.4142 3.44785C15.7893 3.82292 16 4.33163 16 4.86206V6.86206M19 6.86206V20.8621C19 21.3925 18.7893 21.9012 18.4142 22.2763C18.0391 22.6513 17.5304 22.8621 17 22.8621H7C6.46957 22.8621 5.96086 22.6513 5.58579 22.2763C5.21071 21.9012 5 21.3925 5 20.8621V6.86206H19Z"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <path
                d="M10 11.8621V17.8621"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <path
                d="M14 11.8621V17.8621"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
        <svg
            id="cross"
            viewBox="0 0 24 25"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M18 6.86206L6 18.8621"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <path
                d="M6 6.86206L18 18.8621"
                stroke="#797979"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
        <svg
            id="tick"
            viewBox="0 0 24 25"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M20 6.86206L9 17.8621L4 12.8621"
                stroke="#212121"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="/js/runtime.81bbe2fd3eb0811787bb.bundle.js"></script>
    <script src="/js/main.b014b82970910b41aa6f.bundle.js"></script>
    <script>
        const InterviewStatuses = {
            CREATED: 0,
            INVITATION_SENT: 1,
            STARTED: 2,
            FINISHED: 3,
            SUBMITTED: 4
        }

        const MessageRoles = {
            SYSTEM: 'system',
            USER: 'user',
            ASSISTANT: 'assistant'
        }

        var app = new Vue({
            el: '#app',
            data: {
                token: '{{ $token }}',
                interviewId: {{ $interview_id }},
                interviewStatus: {{ $interview_status }},
                intervieweeName: '{{ $interviewee_name }}',
                messages: [],
                userMessageContent: '',
                showLoader: false,
                messagesWithOpenDeletionConfirmation: {}
            },
            computed: {
                isStarted: function() {
                    return this.interviewStatus == InterviewStatuses.STARTED
                },
                isFinished: function() {
                    return this.interviewStatus == InterviewStatuses.FINISHED
                },
                isSubmitted: function () {
                    return this.interviewStatus == InterviewStatuses.SUBMITTED
                },
                outroMessage: function() {
                    return this.messages[this.messages.length - 1]?.content
                }
            },
            methods: {
                startInterview: async function() {
                    this.interviewStatus = InterviewStatuses.STARTED
                    this.showLoader = true

                    var {data} = await axios.post('/api/interviews/' + this.interviewId + '/start', {}, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    this.messages.push({
                        id: data.data.id,
                        content: data.data.content,
                        role: MessageRoles[data.data.role.toUpperCase()]
                    })

                    this.showLoader = false
                },
                endInterview: async function() {
                    this.showLoader = true

                    var {data} = await axios.post('/api/interviews/' + this.interviewId + '/end', {}, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    this.messages.push({
                        id: data.data.id,
                        content: data.data.content,
                        role: MessageRoles[data.data.role.toUpperCase()]
                    })

                    this.interviewStatus = InterviewStatuses.FINISHED

                    this.showLoader = false

                    setTimeout(() => {
                        this.scrollMessagesBottom()
                    }, 10)
                },
                submitInterview: async function() {
                    this.showLoader = true

                    var {data} = await axios.post('/api/interviews/' + this.interviewId + '/submit', {}, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    this.interviewStatus = InterviewStatuses.SUBMITTED

                    this.showLoader = false
                },
                getMessages: async function() {
                    this.showLoader = true

                    var {data} = await axios.get('/api/interviews/' + this.interviewId + '/messages', {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    var messages = []

                    data.data.items.forEach(function(message) {
                        messages.push({
                            id: message.id,
                            content: message.content,
                            role: MessageRoles[message.role.toUpperCase()]
                        })
                    })

                    this.messages = messages

                    this.showLoader = false

                    setTimeout(() => {
                        this.scrollMessagesBottom()
                    }, 10)
                },
                sendMessage: async function() {
                    if(this.userMessageContent != '') {
                        this.showLoader = true

                        var userMessageContent = this.userMessageContent;

                        this.userMessageContent = ''

                        this.messages.push({
                            id: undefined,
                            content: userMessageContent,
                            role: MessageRoles.USER
                        })

                        setTimeout(() => {
                            this.scrollMessagesBottom()
                        }, 10)

                        var {data} = await axios.post('/api/interviews/' + this.interviewId + '/messages', {
                            content: userMessageContent
                        }, {
                            headers: {
                                'Authorization': `Bearer ${this.token}`
                            }
                        })

                        this.messages.at(-1)['id'] = data.data.messagesSet.userMessage.id

                        this.messages.push({
                            id: data.data.messagesSet.chatCompletion.id,
                            content: data.data.messagesSet.chatCompletion.content,
                            role: MessageRoles[data.data.messagesSet.chatCompletion.role.toUpperCase()]
                        })

                        this.showLoader = false

                        setTimeout(() => {
                            this.scrollMessagesBottom()
                        }, 10)
                    }
                },
                skipQuestion: async function() {
                    this.showLoader = true

                    var {data} = await axios.put('/api/interviews/' + this.interviewId + '/messages', {}, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    this.messages.pop()

                    this.messages.push({
                        id: data.data.id,
                        content: data.data.content,
                        role: MessageRoles[data.data.role.toUpperCase()]
                    })

                    this.showLoader = false
                },
                openDeleteConfirmation: async function(messageId) {
                    this.$set(this.messagesWithOpenDeletionConfirmation, messageId, true)
                },
                confirmDeletion: async function(messageId) {
                    this.showLoader = true

                    await axios.delete('/api/interviews/' + this.interviewId + '/messages/' + messageId, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })

                    const messageIndex = this.messages.findIndex(message => message.id === messageId);

                    if (messageIndex !== -1) {
                        this.$delete(this.messages, messageIndex);
                        this.$delete(this.messages, messageIndex - 1);
                    }

                    this.showLoader = false
                },
                cancelDeletion: async function(messageId) {
                    this.$set(this.messagesWithOpenDeletionConfirmation, messageId, false)
                },
                scrollMessagesBottom: function() {
                    const container = this.$refs.messagesContainer

                    container.scrollTop = container.scrollHeight + 100
                }
            },
            mounted: function() {
                if(this.isStarted || this.isFinished) {
                    this.getMessages()
                }
            }
        })
    </script>
</body>
</html>
