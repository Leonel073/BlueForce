<style>
    @page { margin: 24px 30px 42px 30px; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #1A2942;
        margin: 0;
        line-height: 1.5;
    }

    .pdf-header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
        border-bottom: 4px solid #0B2D59;
    }

    .pdf-header td {
        border: none;
        padding: 0 0 14px 0;
        vertical-align: middle;
    }

    .pdf-logo {
        width: 76px;
    }

    .pdf-brand {
        text-align: center;
    }

    .pdf-brand h1 {
        color: #0B2D59;
        font-size: 20px;
        margin: 0 0 2px 0;
        letter-spacing: 0.3px;
    }

    .pdf-brand p {
        color: #5E7491;
        margin: 1px 0;
        font-size: 9px;
    }

    .pdf-title {
        background: #F8FAFC;
        border-left: 6px solid #D9A23D;
        padding: 10px 14px;
        margin: 14px 0 16px 0;
        border-radius: 0;
    }

    .pdf-title h2 {
        color: #0B2D59;
        font-size: 16px;
        margin: 0 0 3px 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .pdf-title p {
        color: #5E7491;
        margin: 0;
        font-size: 9px;
    }

    .pdf-summary {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    .pdf-summary td {
        background: #F8FAFC;
        border: 1px solid #DDE3EC;
        padding: 8px 6px;
        text-align: center;
    }

    .summary-label {
        color: #5E7491;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-value {
        color: #0B2D59;
        font-size: 17px;
        font-weight: bold;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
    }

    .data-table th {
        background: #0B2D59;
        color: #fff;
        border: 1px solid #1a3f6e;
        padding: 8px 7px;
        text-align: left;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        border: 1px solid #DDE3EC;
        padding: 7px;
        vertical-align: top;
        font-size: 9px;
        color: #1A2942;
    }

    .data-table tbody tr:nth-child(even) td {
        background: #F8FAFC;
    }

    .tag {
        display: inline-block;
        border: 1px solid #DDE3EC;
        background: #EEF2F7;
        color: #0B2D59;
        padding: 2px 6px;
        font-size: 8px;
        font-weight: bold;
        border-radius: 2px;
    }

    .tag-success {
        background: #D4EDDA;
        border-color: #0D9E6E;
        color: #065F46;
    }

    .tag-danger {
        background: #F8D7DA;
        border-color: #DC2626;
        color: #991B1B;
    }

    .tag-gold {
        background: #FDF4E0;
        border-color: #D9A23D;
        color: #92400E;
    }

    .tag-navy {
        background: #E8EFF6;
        border-color: #0B2D59;
        color: #0B2D59;
    }

    .muted { color: #5E7491; }
    .strong-navy { color: #0B2D59; font-weight: bold; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }

    .pdf-footer {
        position: fixed;
        bottom: -28px;
        left: 0;
        right: 0;
        border-top: 1px solid #DDE3EC;
        padding-top: 6px;
        text-align: center;
        color: #5E7491;
        font-size: 8px;
    }

    .pdf-footer strong {
        color: #0B2D59;
    }

    .page-break {
        page-break-before: always;
    }
</style>
