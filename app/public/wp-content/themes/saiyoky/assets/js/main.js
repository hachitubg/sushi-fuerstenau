(() => {
	const toggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.primary-navigation');

	if (toggle && navigation) {
		toggle.addEventListener('click', () => {
			const isOpen = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!isOpen));
			toggle.classList.toggle('is-open', !isOpen);
			navigation.classList.toggle('is-open', !isOpen);
		});
	}

	const header = document.querySelector('.site-header');
	const updateHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 24);
	updateHeader();
	window.addEventListener('scroll', updateHeader, { passive: true });

	const revealItems = document.querySelectorAll('[data-reveal], .scroll-reveal');
	if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-revealed');
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px' });
		revealItems.forEach((item, index) => {
			item.style.setProperty('--reveal-delay', `${Math.min(index % 6, 5) * 70}ms`);
			observer.observe(item);
		});
	} else {
		revealItems.forEach((item) => item.classList.add('is-revealed'));
	}

	const gallery = document.querySelector('[data-menu-gallery]');
	const lightbox = document.querySelector('[data-menu-lightbox]');
	if (gallery && lightbox) {
		const cards = [...gallery.querySelectorAll('[data-menu-index]')];
		const image = lightbox.querySelector('[data-menu-lightbox-image]');
		const title = lightbox.querySelector('[data-menu-lightbox-title]');
		const counter = lightbox.querySelector('[data-menu-lightbox-counter]');
		let current = 0;

		const render = (index) => {
			current = (index + cards.length) % cards.length;
			const card = cards[current];
			image.src = card.dataset.menuSrc;
			image.alt = card.dataset.menuTitle;
			title.textContent = card.dataset.menuTitle;
			counter.textContent = `${current + 1} / ${cards.length}`;
		};
		const open = (index) => {
			render(index);
			lightbox.showModal();
			document.documentElement.classList.add('has-modal');
		};
		const close = () => {
			lightbox.close();
			document.documentElement.classList.remove('has-modal');
		};

		cards.forEach((card) => card.addEventListener('click', () => open(Number(card.dataset.menuIndex))));
		lightbox.querySelector('[data-menu-prev]').addEventListener('click', () => render(current - 1));
		lightbox.querySelector('[data-menu-next]').addEventListener('click', () => render(current + 1));
		lightbox.querySelector('[data-menu-close]').addEventListener('click', close);
		lightbox.addEventListener('click', (event) => {
			if (event.target === lightbox) close();
		});
		lightbox.addEventListener('close', () => document.documentElement.classList.remove('has-modal'));
		document.addEventListener('keydown', (event) => {
			if (!lightbox.open) return;
			if (event.key === 'ArrowLeft') render(current - 1);
			if (event.key === 'ArrowRight') render(current + 1);
		});
	}

	document.querySelectorAll('[data-menu-book]').forEach((book) => {
		const pageData = book.querySelector('[data-menu-book-pages]');
		const stage = book.querySelector('.menu-book__stage');
		const image = book.querySelector('[data-book-image]');
		const pageNumber = book.querySelector('.menu-showcase__page-number');
		const previous = book.querySelector('[data-book-prev]');
		const next = book.querySelector('[data-book-next]');
		const counter = book.querySelector('[data-book-counter]');
		const openButton = book.querySelector('[data-book-open]');
		const lightbox = book.querySelector('[data-book-lightbox]');
		const closeButton = book.querySelector('[data-book-close]');
		const modalImage = book.querySelector('[data-book-modal-image]');
		const modalPrevious = book.querySelector('[data-book-modal-prev]');
		const modalNext = book.querySelector('[data-book-modal-next]');
		if (!pageData || !stage || !image || !previous || !next || !counter) return;

		let pages = [];
		try { pages = JSON.parse(pageData.textContent); } catch (error) { return; }
		if (!pages.length) return;
		let current = 0;
		let touchStart = 0;
		const pageLabel = book.dataset.pageLabel || 'Saiyoky menu, page';
		const pageAlt = (index) => `${pageLabel} ${index + 1}`;
		const preload = (index) => {
			if (pages[index]) { const preloadImage = new Image(); preloadImage.src = pages[index]; }
		};
		const render = (animate = false) => {
			if (animate && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
				stage.classList.remove('is-turning');
				void stage.offsetWidth;
				stage.classList.add('is-turning');
			}
			image.src = pages[current];
			image.alt = pageAlt(current);
			if (pageNumber) pageNumber.textContent = String(current + 1).padStart(2, '0');
			counter.value = `${current + 1} / ${pages.length}`;
			counter.textContent = counter.value;
			if (modalImage) {
				modalImage.src = pages[current];
				modalImage.alt = pageAlt(current);
			}
			previous.disabled = current === 0;
			next.disabled = current >= pages.length - 1;
			if (modalPrevious) modalPrevious.disabled = previous.disabled;
			if (modalNext) modalNext.disabled = next.disabled;
			preload(current - 1);
			preload(current + 1);
		};
		const move = (direction) => {
			current = Math.max(0, Math.min(pages.length - 1, current + direction));
			render(true);
		};
		previous.addEventListener('click', () => move(-1));
		next.addEventListener('click', () => move(1));
		stage.addEventListener('keydown', (event) => {
			if (event.key === 'ArrowLeft') { event.preventDefault(); move(-1); }
			if (event.key === 'ArrowRight') { event.preventDefault(); move(1); }
		});
		stage.addEventListener('touchstart', (event) => { touchStart = event.changedTouches[0].clientX; }, { passive: true });
		stage.addEventListener('touchend', (event) => {
			const distance = event.changedTouches[0].clientX - touchStart;
			if (Math.abs(distance) > 45) move(distance < 0 ? 1 : -1);
		}, { passive: true });
		if (lightbox && openButton && closeButton && modalImage) {
			openButton.addEventListener('click', () => {
				if (typeof lightbox.showModal === 'function') {
					lightbox.showModal();
					document.documentElement.classList.add('has-modal');
				}
			});
			closeButton.addEventListener('click', () => lightbox.close());
			modalPrevious?.addEventListener('click', () => move(-1));
			modalNext?.addEventListener('click', () => move(1));
			lightbox.addEventListener('click', (event) => { if (event.target === lightbox) lightbox.close(); });
			lightbox.addEventListener('close', () => document.documentElement.classList.remove('has-modal'));
			document.addEventListener('keydown', (event) => {
				if (!lightbox.open) return;
				if (event.key === 'ArrowLeft') move(-1);
				if (event.key === 'ArrowRight') move(1);
			});
		}
		render(false);
	});

	const auth = document.querySelector('[data-auth-tabs]');
	if (auth) {
		const buttons = [...auth.querySelectorAll('[data-auth-target]')];
		const panels = [...auth.querySelectorAll('[data-auth-panel]')];
		buttons.forEach((button) => button.addEventListener('click', () => {
			buttons.forEach((item) => item.classList.toggle('is-active', item === button));
			panels.forEach((panel) => panel.hidden = panel.dataset.authPanel !== button.dataset.authTarget);
		}));
	}
})();
