<footer style="background-color: var(--bg-surface); border-top: 1px solid var(--border-color); padding: 15px; text-align: center; color: var(--text-secondary); font-size: 13px;">
    &copy; {{ date('Y') }} {{ \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'Kurayami' }}.
</footer>