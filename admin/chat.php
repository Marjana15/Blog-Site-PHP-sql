<?php
include '../partials/header.php';

// Ensure the user is logged in
if (!isset($_SESSION['user-id'])) {
    header('Location: ' . ROOT_URL . 'signin.php');
    exit();
}

$current_user_id = $_SESSION['user-id'];

// Fetch all other users for the user list
$users_query = "SELECT id, firstname, lastname FROM users WHERE id != $current_user_id";
$users_result = mysqli_query($connection, $users_query);

$users = [];
while ($user = mysqli_fetch_assoc($users_result)) {
    $users[] = $user;
}
?>

<div id="chat-app">
    <div class="chat-container">
        <aside class="user-list">
            <h2>Users</h2>
            <ul>
                <li v-for="user in users" :key="user.id">
                    <button @click="selectUser(user.id, `${user.firstname} ${user.lastname}`)">
                        {{ user.firstname }} {{ user.lastname }}
                    </button>
                </li>
            </ul>
        </aside>

        <main class="chat-window">
            <div v-if="recipient">
                <h2>Chat with {{ recipient.name }}</h2>
                <div class="messages">
                    <div 
                        v-for="message in messages" 
                        :key="message.id" 
                        :class="{'message sent': message.sender_id == currentUserId, 'message received': message.sender_id != currentUserId}">
                        <strong>{{ message.sender_id == currentUserId ? 'You' : message.sender_name }}:</strong>
                        <p>{{ message.message }}</p>
                        <small>{{ new Date(message.timestamp).toLocaleString() }}</small>
                    </div>
                </div>

                <form @submit.prevent="sendMessage" class="chat-form">
                    <textarea v-model="newMessage" placeholder="Type a message..." required></textarea>
                    <button type="submit">Send</button>
                </form>

            </div>
            <p v-else>Select a user to start chatting.</p>
        </main>
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            users: <?php echo json_encode($users); ?>,
            currentUserId: <?php echo $current_user_id; ?>,
            recipient: null,
            messages: [],
            newMessage: '',
        };
    },
    methods: {
        async selectUser(id, name) {
            this.recipient = { id, name };
            await this.fetchMessages();
            this.startPollingMessages();
        },
        async fetchMessages() {
            if (this.recipient) {
                const response = await fetch(`fetch-messages.php?sender_id=${this.currentUserId}&receiver_id=${this.recipient.id}`);
                this.messages = await response.json();
            }
        },
        async sendMessage() {
            const payload = new URLSearchParams();
            payload.append('sender_id', this.currentUserId);
            payload.append('receiver_id', this.recipient.id);
            payload.append('message', this.newMessage);

            const response = await fetch('send-message.php', {
                method: 'POST',
                body: payload,
            });

            if (response.ok) {
                this.newMessage = '';
                await this.fetchMessages();
            }
        },
        startPollingMessages() {
            setInterval(() => {
                this.fetchMessages();
            }, 2000);
        },
    },
}).mount('#chat-app');
</script>


<?php include '../partials/footer.php'; ?>

<style>



</style>