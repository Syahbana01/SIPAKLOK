import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const menu = document.querySelector('[data-nav-menu]');

    toggle?.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!expanded));
        menu?.classList.toggle('open', !expanded);
    });

    document.querySelector('[data-confirm-cancel]')?.addEventListener('submit', (event) => {
        if (!window.confirm('Kirim pengajuan pembatalan untuk ditinjau petugas?')) {
            event.preventDefault();
        }
    });

    const form = document.querySelector('[data-booking-form]');

    if (!form) return;

    const quantity = form.querySelector('[data-quantity]');
    const passengerList = form.querySelector('[data-passenger-list]');
    const unitPrice = Number(form.dataset.unitPrice);
    const total = form.querySelector('[data-total]');
    const formatCurrency = (amount) => `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(amount)}`;

    const passengerMarkup = (index) => `
        <legend>Penumpang ${index + 1}</legend>
        <label class="field"><span>Nama lengkap</span><input name="penumpang[${index}][nama_lengkap]" required maxlength="100" placeholder="Sesuai identitas"></label>
        <div class="field-row">
            <label class="field"><span>Umur</span><input type="number" name="penumpang[${index}][umur]" required min="0" max="120" placeholder="Tahun"></label>
            <label class="field"><span>Jenis kelamin</span><select name="penumpang[${index}][jenis_kelamin]" required><option value="">Pilih</option><option value="laki-laki">Laki-laki</option><option value="perempuan">Perempuan</option></select></label>
        </div>`;

    const updatePassengerForms = () => {
        const count = Number(quantity.value);
        let cards = passengerList.querySelectorAll('[data-passenger]');

        while (cards.length < count) {
            const card = document.createElement('fieldset');
            card.className = 'passenger-card';
            card.dataset.passenger = '';
            passengerList.append(card);
            cards = passengerList.querySelectorAll('[data-passenger]');
        }

        cards.forEach((card, index) => {
            if (!card.querySelector('legend')) card.insertAdjacentHTML('afterbegin', passengerMarkup(index));
            else card.querySelector('legend').textContent = `Penumpang ${index + 1}`;

            card.hidden = index >= count;
            card.querySelectorAll('input, select').forEach((input) => {
                input.disabled = index >= count;
                input.name = input.name.replace(/penumpang\[\d+\]/, `penumpang[${index}]`);
            });
        });

        total.textContent = formatCurrency(unitPrice * count);
    };

    quantity.addEventListener('change', updatePassengerForms);
    updatePassengerForms();
});
