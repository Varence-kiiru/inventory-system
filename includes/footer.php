<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p>© <?php echo date('Y'); ?> The Olivian Group Limited. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-end">
                <p>Version 1.0.0</p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: #fff;
    padding: 1rem;
    position: fixed;
    bottom: 0;
    left: 250px;
    right: 0;
    border-top: 1px solid #eee;
    z-index: 1000;
}

@media (max-width: 768px) {
    .footer {
        left: 0;
    }
}
</style>
