{{-- ✅ Floating Flash Messages --}}
<div id="flash-container" class="fixed top-5 right-5 z-[9999] space-y-4 w-[350px]">

    @if(session('success'))
        <div class="flash-message success">
            <div class="icon">
                ✓
            </div>
            <div class="content">
                <h4>Success</h4>
                <p>{{ session('success') }}</p>
            </div>
            <button class="close-btn">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-message error">
            <div class="icon">
                ✕
            </div>
            <div class="content">
                <h4>Error</h4>
                <p>{{ session('error') }}</p>
            </div>
            <button class="close-btn">&times;</button>
        </div>
    @endif

</div>

<style>
.flash-message {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid;
    background: white;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    animation: slideIn 0.4s ease;
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.flash-message.success {
    border-color: #22c55e;
    background: #f0fdf4;
    color: #166534;
}

.flash-message.error {
    border-color: #ef4444;
    background: #fef2f2;
    color: #991b1b;
}

.flash-message .icon {
    font-weight: bold;
    font-size: 18px;
}

.flash-message h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.flash-message p {
    margin: 2px 0 0;
    font-size: 13px;
}

.close-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: inherit;
}

.flash-message.fade-out {
    opacity: 0;
    transform: translateY(-10px);
}

@keyframes slideIn {
    from { transform: translateX(40px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const messages = document.querySelectorAll('.flash-message');

    messages.forEach((msg) => {

        // Auto close
        const autoClose = setTimeout(() => {
            closeMessage(msg);
        }, 5000);

        // Manual close
        const btn = msg.querySelector('.close-btn');
        btn.addEventListener('click', () => {
            clearTimeout(autoClose);
            closeMessage(msg);
        });
    });

    function closeMessage(msg) {
        msg.classList.add('fade-out');
        setTimeout(() => {
            msg.remove();
        }, 500);
    }
});
</script>



