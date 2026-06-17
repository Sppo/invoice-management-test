// формат дати без часу
export function formatDate(value: string | null | undefined): string {
    if (!value) return '—';
    const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!match) return '—';
    const [, year, month, day] = match;
    return `${day}.${month}.${year}`;
}

// формат дати з часом
export function formatDateTime(value: string | null | undefined): string {
    if (!value) return '—';
    const match = value.match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/);
    if (!match) return '—';
    const [, year, month, day, hours, minutes] = match;
    return `${day}.${month}.${year} ${hours}:${minutes}`;
}

// формат валюти
export function formatMoney(value: string | number | null | undefined, currency = 'UAH'): string {
    if (value === null || value === undefined || value === '') return '—';
    const num = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(num)) return '—';
    const fixed = num.toFixed(2);
    const [intPart = '0', decPart = '00'] = fixed.split('.');
    const withSpaces = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return `${withSpaces},${decPart} ${currency}`;
}
