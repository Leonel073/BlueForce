<style>
    :root {
        --bf-navy:       #0B2D59;
        --bf-navy-dark:  #071e3d;
        --bf-navy-light: #1a3f6e;
        --bf-blue:       #2E608C;
        --bf-blue-light: #4a82b0;
        --bf-blue-pale:  #E8EFF6;
        --bf-gold:       #D9A23D;
        --bf-gold-light: #F0C870;
        --bf-gold-dark:  #b07d1a;
        --bf-gold-pale:  #FDF4E0;
        --bf-light:      #F8FAFC;
        --bf-light2:     #EEF2F7;
        --bf-border:     #DDE3EC;
        --bf-text:       #1A2942;
        --bf-muted:      #5E7491;
        --bf-success:    #0D9E6E;
        --bf-success-light: #D4EDDA;
        --bf-danger:     #DC2626;
        --bf-danger-light: #F8D7DA;
        --bf-warning:    #D97706;
        --bf-warning-light: #FFF3CD;
        --bf-info:       #2563EB;
        --bf-info-light: #CCE5FF;
        --bf-purple:     #7C3AED;
        --bf-purple-light: #E8D5F5;
    }

    .report-container {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        color: var(--bf-text);
        background: linear-gradient(160deg, #EEF2F7 0%, #F8FAFC 100%);
        min-height: 100vh;
        padding: 1.75rem;
    }

    .report-hero {
        background: linear-gradient(135deg, var(--bf-navy-dark) 0%, var(--bf-navy) 45%, var(--bf-blue) 100%);
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(11,45,89,0.22), 0 2px 8px rgba(11,45,89,0.12);
        border: none;
        margin-bottom: 1.75rem;
    }
    .report-hero::after {
        content: "";
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--bf-gold-dark), var(--bf-gold), var(--bf-gold-light), var(--bf-gold), var(--bf-gold-dark));
    }
    .report-hero::before {
        content: "";
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        border: 2px solid rgba(255,255,255,0.06);
    }
    .report-hero img {
        width: 88px; height: 88px;
        object-fit: contain;
        background: white;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.25), 0 0 0 3px var(--bf-gold);
        flex-shrink: 0;
    }
    .report-hero h1 {
        font-weight: 800;
        letter-spacing: -0.5px;
        color: white;
        font-size: 1.75rem;
        margin-bottom: 0.3rem;
    }
    .report-hero p {
        color: rgba(255,255,255,0.8);
        font-size: 1rem;
        margin-bottom: 0;
    }
    .report-hero-badge {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        border-radius: 20px;
        padding: 0.3rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.6rem 1.4rem;
        text-align: center;
        border: 1px solid var(--bf-border);
        box-shadow: 0 2px 12px rgba(11,45,89,0.06);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(11,45,89,0.1);
    }
    .stat-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--stat-accent, var(--bf-navy));
        border-radius: 14px 14px 0 0;
    }
    .stat-card .stat-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 0.9rem;
        background: var(--stat-icon-bg, rgba(11,45,89,0.08));
        color: var(--stat-accent, var(--bf-navy));
    }
    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 0.3rem;
        color: var(--stat-accent, var(--bf-navy));
    }
    .stat-card .stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: var(--bf-muted);
    }

    .stat-navy   { --stat-accent: var(--bf-navy);     --stat-icon-bg: rgba(11,45,89,0.08); }
    .stat-blue   { --stat-accent: var(--bf-info);     --stat-icon-bg: rgba(37,99,235,0.08); }
    .stat-gold   { --stat-accent: var(--bf-gold-dark);--stat-icon-bg: rgba(217,162,61,0.1); }
    .stat-success{ --stat-accent: var(--bf-success);  --stat-icon-bg: rgba(13,158,110,0.1); }
    .stat-danger { --stat-accent: var(--bf-danger);   --stat-icon-bg: rgba(220,38,38,0.08); }
    .stat-purple { --stat-accent: var(--bf-purple);   --stat-icon-bg: rgba(124,58,237,0.08); }

    .report-card {
        background: white;
        border: 1px solid var(--bf-border);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(11,45,89,0.04);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .report-card-hover:hover {
        box-shadow: 0 8px 24px rgba(11,45,89,0.1);
        transform: translateY(-2px);
    }
    .report-card-header {
        background: linear-gradient(90deg, rgba(11,45,89,0.04), transparent);
        border-bottom: 1px solid var(--bf-border);
        padding: 1.1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .report-card-title {
        color: var(--bf-navy);
        font-weight: 700;
        margin: 0;
        font-size: 1rem;
        letter-spacing: -0.2px;
    }
    .report-card-body {
        padding: 1.5rem;
    }

    .report-filter-area {
        background: white;
        padding: 1.4rem 1.5rem;
        border-radius: 14px;
        border: 1px solid var(--bf-border);
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(11,45,89,0.04);
    }
    .report-filter-area label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--bf-navy);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 0.35rem;
        display: block;
    }
    .report-filter-area .form-control,
    .report-filter-area .form-select {
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        background: var(--bf-light);
        color: var(--bf-text);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .report-filter-area .form-control:focus,
    .report-filter-area .form-select:focus {
        border-color: var(--bf-blue);
        box-shadow: 0 0 0 3px rgba(46,96,140,0.15);
        background: white;
        outline: none;
    }
    .filter-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-bf-primary {
        background: linear-gradient(135deg, var(--bf-navy), var(--bf-blue));
        color: white; border: none;
        font-weight: 600; font-size: 0.875rem;
        padding: 0.55rem 1.1rem; border-radius: 8px;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.2s, transform 0.1s;
        text-decoration: none;
    }
    .btn-bf-primary:hover { color: white; opacity: 0.88; transform: translateY(-1px); }

    .btn-bf-secondary {
        background: white; color: var(--bf-navy);
        border: 1.5px solid var(--bf-border);
        font-weight: 600; font-size: 0.875rem;
        padding: 0.55rem 1.1rem; border-radius: 8px;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: all 0.15s; text-decoration: none;
    }
    .btn-bf-secondary:hover { background: var(--bf-light2); color: var(--bf-navy); border-color: #b0becc; }

    .btn-bf-gold {
        background: linear-gradient(135deg, var(--bf-gold), var(--bf-gold-dark));
        color: white; border: none;
        font-weight: 600; font-size: 0.875rem;
        padding: 0.55rem 1.1rem; border-radius: 8px;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.2s, transform 0.1s; text-decoration: none;
    }
    .btn-bf-gold:hover { color: white; opacity: 0.88; transform: translateY(-1px); }

    .btn-bf-danger {
        background: linear-gradient(135deg, #DC2626, #991B1B);
        color: white; border: none;
        font-weight: 600; font-size: 0.875rem;
        padding: 0.55rem 1.1rem; border-radius: 8px;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.2s, transform 0.1s; text-decoration: none;
    }
    .btn-bf-danger:hover { color: white; opacity: 0.88; transform: translateY(-1px); }

    .btn-bf-success {
        background: linear-gradient(135deg, #059669, #047857);
        color: white; border: none;
        font-weight: 600; font-size: 0.875rem;
        padding: 0.55rem 1.1rem; border-radius: 8px;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.2s; text-decoration: none;
    }
    .btn-bf-success:hover { color: white; opacity: 0.88; }

    .report-table-wrapper {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--bf-border);
        background: white;
        box-shadow: 0 2px 10px rgba(11,45,89,0.04);
    }
    .report-table {
        margin-bottom: 0; width: 100%; border-collapse: collapse;
    }
    .report-table thead th {
        background: var(--bf-navy);
        color: white;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0.95rem 1rem;
        border: none;
        font-weight: 700;
        white-space: nowrap;
    }
    .report-table thead th:first-child { padding-left: 1.4rem; }
    .report-table thead th:last-child  { padding-right: 1.4rem; }
    .report-table tbody tr:nth-child(even) td { background: rgba(11,45,89,0.018); }
    .report-table tbody td {
        padding: 0.95rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--bf-border);
        font-size: 0.88rem;
        color: var(--bf-text);
    }
    .report-table tbody td:first-child { padding-left: 1.4rem; }
    .report-table tbody td:last-child  { padding-right: 1.4rem; }
    .report-table tbody tr:hover td { background: rgba(46,96,140,0.045); }
    .report-table tbody tr:last-child td { border-bottom: none; }

    .badge-bf {
        padding: 0.3em 0.7em;
        font-size: 0.72em;
        font-weight: 700;
        border-radius: 5px;
        letter-spacing: 0.4px;
        display: inline-block;
        vertical-align: middle;
        white-space: nowrap;
    }
    .badge-bf-navy    { background: rgba(11,45,89,0.1);    color: var(--bf-navy);     border: 1px solid rgba(11,45,89,0.18); }
    .badge-bf-blue    { background: rgba(46,96,140,0.1);   color: var(--bf-blue);     border: 1px solid rgba(46,96,140,0.2); }
    .badge-bf-gold    { background: rgba(217,162,61,0.12); color: var(--bf-gold-dark);border: 1px solid rgba(217,162,61,0.3); }
    .badge-bf-success { background: rgba(13,158,110,0.1);  color: #065F46;            border: 1px solid rgba(13,158,110,0.2); }
    .badge-bf-warning { background: rgba(217,119,6,0.1);   color: #92400E;            border: 1px solid rgba(217,119,6,0.25); }
    .badge-bf-danger  { background: rgba(220,38,38,0.1);   color: #991B1B;            border: 1px solid rgba(220,38,38,0.2); }
    .badge-bf-info    { background: rgba(37,99,235,0.1);   color: #1E40AF;            border: 1px solid rgba(37,99,235,0.2); }
    .badge-bf-gray    { background: #F1F5F9;               color: #475569;            border: 1px solid #CBD5E1; }
    .badge-bf-purple  { background: rgba(124,58,237,0.1);  color: #5B21B6;            border: 1px solid rgba(124,58,237,0.2); }

    .report-accordion .accordion-item {
        border: 1px solid var(--bf-border) !important;
        border-radius: 10px !important;
        margin-bottom: 0.65rem;
        background: white;
        box-shadow: 0 1px 5px rgba(11,45,89,0.04);
        overflow: hidden;
    }
    .report-accordion .accordion-button {
        border-radius: 10px !important;
        background: white;
        color: var(--bf-navy);
        font-weight: 700;
        font-size: 0.95rem;
        padding: 1.1rem 1.4rem;
        box-shadow: none !important;
    }
    .report-accordion .accordion-button:not(.collapsed) {
        background: linear-gradient(90deg, rgba(11,45,89,0.05), transparent);
        color: var(--bf-navy);
        border-bottom: 1px solid var(--bf-border);
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .report-accordion .accordion-body {
        padding: 1.4rem;
        background: white;
    }

    .menu-card {
        display: block; text-decoration: none; height: 100%;
    }
    .menu-card .report-card {
        height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 2.2rem 1.5rem;
        border-top: 4px solid transparent;
        margin-bottom: 0;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        cursor: pointer;
    }
    .menu-card:hover .report-card {
        border-top-color: var(--bf-gold);
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(11,45,89,0.12);
    }
    .menu-card .icon-wrapper {
        width: 72px; height: 72px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.1rem;
        margin-bottom: 1.1rem;
        transition: transform 0.2s;
    }
    .menu-card:hover .icon-wrapper { transform: scale(1.08); }
    .menu-card .menu-title { color: var(--bf-navy); font-weight: 700; font-size: 1.15rem; margin-bottom: 0.4rem; }
    .menu-card .menu-desc  { color: var(--bf-muted); font-size: 0.84rem; text-align: center; margin: 0; line-height: 1.4; }

    .icon-personas { color: var(--bf-success);   background: rgba(13,158,110,0.1)  !important; }
    .icon-usuarios { color: var(--bf-navy);       background: rgba(11,45,89,0.1)   !important; }
    .icon-deptos   { color: var(--bf-gold-dark);  background: rgba(217,162,61,0.1) !important; }
    .icon-docs     { color: var(--bf-info);       background: rgba(37,99,235,0.1)  !important; }
    .icon-deriv    { color: var(--bf-purple);     background: rgba(124,58,237,0.1) !important; }

    .persona-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, var(--bf-navy), var(--bf-blue));
        color: white; font-weight: 800; font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .report-divider {
        border: none; height: 2px;
        background: linear-gradient(90deg, transparent, var(--bf-border), transparent);
        margin: 1.5rem 0;
    }
</style>
