/*!
 * Diegor Accesibilidad – lógica del widget.
 * Diseñado y desarrollado por Diegor.tech · https://diegor.tech/
 * Sin dependencias. Guarda las preferencias del visitante en localStorage.
 */
(function () {
	'use strict';

	var C = window.DA_CONFIG || {};
	var T = C.i18n || {};
	var root = document.getElementById('da-root');
	if (!root) {
		return;
	}

	var html = document.documentElement;
	var KEY = 'da_prefs_v1';
	var TOGGLES = ['links', 'font', 'spacing', 'focus', 'cursor', 'guide', 'magnifier', 'motion', 'clickread', 'captions', 'bigcaptions'];
	var CONTRASTS = ['high', 'dark', 'invert', 'gray'];
	var XHTML = 'http://www.w3.org/1999/xhtml';
	var reduceMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

	/* ---------- Utilidades ---------- */
	function $(sel, ctx) { return (ctx || root).querySelector(sel); }
	function $$(sel, ctx) { return Array.prototype.slice.call((ctx || root).querySelectorAll(sel)); }
	function fmt(str, val) { return String(str || '').replace('%s', val); }
	function outside(el) { return !!el && !root.contains(el); }
	function visible(el) { return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length); }
	function clean(text) { return String(text || '').replace(/\s+/g, ' ').trim(); }

	/* ---------- Preferencias ---------- */
	function defaults() {
		return {
			scale: 0, contrast: 'none', rate: 1,
			links: false, font: false, spacing: false, focus: false, cursor: false,
			guide: false, magnifier: false, motion: reduceMotion, clickread: false,
			captions: false, bigcaptions: false
		};
	}
	function load() {
		var p = defaults();
		try {
			var raw = window.localStorage.getItem(KEY);
			if (raw) {
				var saved = JSON.parse(raw);
				Object.keys(p).forEach(function (k) {
					if (saved && Object.prototype.hasOwnProperty.call(saved, k)) { p[k] = saved[k]; }
				});
			}
		} catch (e) { /* almacenamiento no disponible */ }
		return p;
	}
	function save() {
		try { window.localStorage.setItem(KEY, JSON.stringify(prefs)); } catch (e) { /* sin persistencia */ }
	}
	var prefs = load();

	/* ---------- Anuncios para lectores de pantalla ---------- */
	var live = $('.da-live');
	function announce(msg) {
		if (!live || !msg) { return; }
		live.textContent = '';
		window.setTimeout(function () { live.textContent = msg; }, 80);
	}

	/* ---------- Panel ---------- */
	var trigger = $('.da-trigger');
	var panel = $('#da-panel');

	function openPanel() {
		panel.hidden = false;
		trigger.setAttribute('aria-expanded', 'true');
		var first = $('.da-close', panel);
		if (first) { first.focus(); }
	}
	function closePanel(returnFocus) {
		if (panel.hidden) { return; }
		panel.hidden = true;
		trigger.setAttribute('aria-expanded', 'false');
		if (returnFocus !== false) { trigger.focus(); }
	}
	function togglePanel() {
		if (panel.hidden) { openPanel(); } else { closePanel(); }
	}
	trigger.addEventListener('click', togglePanel);
	$('.da-close', panel).addEventListener('click', function () { closePanel(); });

	document.addEventListener('click', function (e) {
		if (!panel.hidden && outside(e.target)) { closePanel(false); }
	});

	/* ---------- Tamaño del texto ---------- */
	var STEP = 0.1, MIN = -2, MAX = 8;
	var originals = new WeakMap();
	var touched = [];
	var SKIP = /^(SCRIPT|STYLE|NOSCRIPT|TEMPLATE|IFRAME|VIDEO|AUDIO|IMG|CANVAS|BR|HR|OPTION)$/;

	function ownText(el) {
		for (var n = el.firstChild; n; n = n.nextSibling) {
			if (n.nodeType === 3 && n.nodeValue.trim()) { return true; }
		}
		return false;
	}
	function restoreScale() {
		touched.forEach(function (el) {
			var o = originals.get(el);
			el.style.removeProperty('font-size');
			if (o && o.inline) { el.style.setProperty('font-size', o.inline, o.priority); }
		});
		touched = [];
	}
	function applyScale() {
		var factor = 1 + prefs.scale * STEP;
		restoreScale();

		if (prefs.scale !== 0) {
			var all = document.body.getElementsByTagName('*');
			var els = [];
			for (var i = 0; i < all.length; i++) {
				var el = all[i];
				if (el.namespaceURI !== XHTML || SKIP.test(el.tagName) || !outside(el) || !ownText(el)) { continue; }
				els.push(el);
			}
			// Primero se miden todos (sin escalar) y luego se aplica, para no acumular tamaños heredados.
			var sizes = els.map(function (el) {
				if (!originals.has(el)) {
					originals.set(el, {
						inline: el.style.getPropertyValue('font-size'),
						priority: el.style.getPropertyPriority('font-size')
					});
				}
				return parseFloat(window.getComputedStyle(el).fontSize) || 16;
			});
			els.forEach(function (el, idx) {
				el.style.setProperty('font-size', (sizes[idx] * factor).toFixed(2) + 'px', 'important');
				touched.push(el);
			});
		}

		var out = $('#da-scale-value');
		if (out) { out.textContent = Math.round(factor * 100) + '%'; }
	}

	/* ---------- Clases de modo en <html> ---------- */
	function applyClasses() {
		CONTRASTS.forEach(function (c) { html.classList.toggle('da-c-' + c, prefs.contrast === c); });
		TOGGLES.forEach(function (k) { html.classList.toggle('da-on-' + k, !!prefs[k]); });
	}

	function syncUI() {
		$$('[data-da-toggle]').forEach(function (b) {
			b.setAttribute('aria-pressed', prefs[b.getAttribute('data-da-toggle')] ? 'true' : 'false');
		});
		$$('[data-da-contrast]').forEach(function (b) {
			b.setAttribute('aria-pressed', prefs.contrast === b.getAttribute('data-da-contrast') ? 'true' : 'false');
		});
		var rate = $('#da-rate');
		if (rate) { rate.value = String(prefs.rate); }
	}

	function commit(withScale) {
		save();
		applyClasses();
		if (withScale) { applyScale(); }
		syncUI();
	}

	/* ---------- Animaciones ---------- */
	function applyMotion() {
		if (!prefs.motion) { return; }
		$$('video', document).forEach(function (v) {
			if (outside(v) && v.autoplay && !v.paused) {
				try { v.pause(); } catch (e) { /* noop */ }
			}
		});
	}

	/* ---------- Guía de lectura y lupa ---------- */
	var guide = $('.da-guide');
	var mag = $('.da-magnifier');

	function positionMag(e) {
		var w = mag.offsetWidth, h = mag.offsetHeight;
		var x = Math.min(e.clientX + 18, window.innerWidth - w - 8);
		var y = e.clientY + 26;
		if (y + h > window.innerHeight - 8) { y = e.clientY - h - 16; }
		mag.style.left = Math.max(8, x) + 'px';
		mag.style.top = Math.max(8, y) + 'px';
	}

	document.addEventListener('mousemove', function (e) {
		if (prefs.guide && guide) { guide.style.transform = 'translateY(' + Math.max(0, e.clientY - 22) + 'px)'; }
		if (prefs.magnifier && mag && mag.classList.contains('is-on')) { positionMag(e); }
	}, { passive: true });

	document.addEventListener('mouseover', function (e) {
		if (!prefs.magnifier || !mag) { return; }
		var el = e.target && e.target.closest
			? e.target.closest('p,h1,h2,h3,h4,h5,h6,li,a,button,label,td,th,dt,dd,figcaption,blockquote,span')
			: null;
		var text = el && outside(el) ? clean(el.innerText) : '';
		if (!text) { mag.classList.remove('is-on'); return; }
		mag.textContent = text.length > 280 ? text.slice(0, 277) + '…' : text;
		mag.classList.add('is-on');
		positionMag(e);
	});

	/* ---------- Lector de voz ---------- */
	var synth = window.speechSynthesis;
	var ttsOK = !!(synth && window.SpeechSynthesisUtterance);
	var voice = null;

	function pickVoice() {
		var voices = synth.getVoices() || [];
		var order = ['es-co', 'es-419', 'es-mx', 'es-us', 'es-es'];
		var norm = function (v) { return String(v.lang || '').toLowerCase().replace('_', '-'); };
		voice = null;
		for (var i = 0; i < order.length && !voice; i++) {
			for (var j = 0; j < voices.length; j++) {
				if (norm(voices[j]) === order[i]) { voice = voices[j]; break; }
			}
		}
		if (!voice) {
			voice = voices.filter(function (v) { return norm(v).indexOf('es') === 0; })[0] || null;
		}
	}

	if (ttsOK) {
		pickVoice();
		if (synth.addEventListener) { synth.addEventListener('voiceschanged', pickVoice); }
		else { synth.onvoiceschanged = pickVoice; }
	} else {
		$$('[data-da-tts]').forEach(function (el) { el.hidden = true; });
		var noTts = $('#da-tts-unsupported');
		if (noTts) { noTts.hidden = false; }
	}

	// Trozos cortos: algunos navegadores cortan las locuciones largas.
	function chunk(text) {
		var pieces = clean(text).match(/[^.!?;:]+[.!?;:]*/g) || [];
		var out = [], buf = '';
		pieces.forEach(function (p) {
			p = p.trim();
			if (!p) { return; }
			if (buf && (buf + ' ' + p).length > 180) { out.push(buf); buf = p; }
			else { buf = buf ? buf + ' ' + p : p; }
		});
		if (buf) { out.push(buf); }
		var final = [];
		out.forEach(function (s) {
			while (s.length > 240) {
				var cut = s.lastIndexOf(' ', 220);
				cut = cut > 40 ? cut : 220;
				final.push(s.slice(0, cut));
				s = s.slice(cut).trim();
			}
			if (s) { final.push(s); }
		});
		return final;
	}

	function speak(text) {
		if (!ttsOK) { announce(T.noTTS); return false; }
		var parts = chunk(text);
		synth.cancel();
		if (!parts.length) { announce(T.nothing); return false; }
		parts.forEach(function (p) {
			var u = new window.SpeechSynthesisUtterance(p);
			if (voice) { u.voice = voice; u.lang = voice.lang; } else { u.lang = html.lang || 'es-CO'; }
			u.rate = Number(prefs.rate) || 1;
			synth.speak(u);
		});
		return true;
	}

	function readableText() {
		var sel = window.getSelection ? clean(String(window.getSelection())) : '';
		if (sel && !root.contains(window.getSelection().anchorNode)) { return sel; }

		var scope = document.querySelector('main, [role="main"], #main, #content, .site-content, article') || document.body;
		var nodes = scope.querySelectorAll('h1,h2,h3,h4,h5,h6,p,li,blockquote,figcaption,dt,dd,th,td');
		var picked = [];
		for (var i = 0; i < nodes.length; i++) {
			var el = nodes[i];
			if (!outside(el) || !visible(el)) { continue; }
			if (el.closest('nav, [aria-hidden="true"], [hidden], noscript')) { continue; }
			if (picked.some(function (p) { return p.contains(el); })) { continue; }
			picked.push(el);
		}
		return picked
			.map(function (el) { var t = clean(el.innerText); return t && !/[.!?:;]$/.test(t) ? t + '.' : t; })
			.filter(Boolean)
			.join(' ');
	}

	document.addEventListener('click', function (e) {
		if (!prefs.clickread || !ttsOK || !outside(e.target) || !e.target.closest) { return; }
		if (e.target.closest('a, button, input, select, textarea, label, [role="button"]')) { return; }
		var el = e.target.closest('p,h1,h2,h3,h4,h5,h6,li,td,th,blockquote,figcaption,dt,dd');
		var text = el ? clean(el.innerText) : '';
		if (text) { speak(text); }
	});

	/* ---------- Subtítulos ---------- */
	function applyCaptions(notify) {
		var count = 0;

		$$('video', document).forEach(function (v) {
			if (!outside(v) || !v.textTracks || !v.textTracks.length) { return; }
			var tracks = v.textTracks, best = -1, bestScore = -1;
			for (var i = 0; i < tracks.length; i++) {
				var kind = tracks[i].kind;
				if (kind !== 'subtitles' && kind !== 'captions') { continue; }
				var score = (kind === 'captions' ? 2 : 0) + (/^es/i.test(tracks[i].language || '') ? 1 : 0);
				if (score > bestScore) { best = i; bestScore = score; }
			}
			if (best < 0) { return; }
			count++;
			for (var j = 0; j < tracks.length; j++) {
				if (tracks[j].kind !== 'subtitles' && tracks[j].kind !== 'captions') { continue; }
				if (prefs.captions) { tracks[j].mode = j === best ? 'showing' : 'disabled'; }
				else if (v.hasAttribute('data-da-cc')) { tracks[j].mode = 'disabled'; }
			}
			if (prefs.captions) { v.setAttribute('data-da-cc', '1'); } else { v.removeAttribute('data-da-cc'); }
		});

		$$('iframe', document).forEach(function (f) {
			var src = f.getAttribute('src') || '';
			if (!/youtube(-nocookie)?\.com\/embed\//i.test(src)) { return; }
			var url;
			try { url = new URL(src, window.location.href); } catch (e) { return; }
			count++;
			if (prefs.captions) {
				if (url.searchParams.get('cc_load_policy') === '1') { return; }
				url.searchParams.set('cc_load_policy', '1');
				url.searchParams.set('cc_lang_pref', 'es');
				url.searchParams.set('hl', 'es');
				f.setAttribute('data-da-cc', '1');
				f.setAttribute('src', url.toString());
			} else if (f.hasAttribute('data-da-cc')) {
				url.searchParams.delete('cc_load_policy');
				f.removeAttribute('data-da-cc');
				f.setAttribute('src', url.toString());
			}
		});

		if (notify) {
			if (!prefs.captions) { announce(T.captionsOff); }
			else { announce(count ? fmt(T.captionsOn, count) : T.noCaptions); }
		}
	}

	/* ---------- Intérprete LSC ---------- */
	var lsc = $('.da-lsc');
	var lscBtn = $('[data-da-action="lsc-open"]');

	function openLsc() {
		if (!lsc) { return; }
		var body = $('.da-lsc-body', lsc);
		var tpl = $('#da-lsc-tpl');
		if (body && tpl && !body.firstChild) { body.appendChild(tpl.content.cloneNode(true)); }
		lsc.hidden = false;
		if (lscBtn) { lscBtn.setAttribute('aria-expanded', 'true'); }
		var close = $('.da-lsc-close', lsc);
		if (close) { close.focus(); }
	}
	function closeLsc() {
		if (!lsc || lsc.hidden) { return; }
		lsc.hidden = true;
		var body = $('.da-lsc-body', lsc);
		if (body) { body.innerHTML = ''; } // detiene la reproducción
		if (lscBtn) { lscBtn.setAttribute('aria-expanded', 'false'); }
		if (!panel.hidden && lscBtn) { lscBtn.focus(); } else { trigger.focus(); }
	}
	if (lsc) { $('.da-lsc-close', lsc).addEventListener('click', closeLsc); }

	/* ---------- Transcripción ---------- */
	var tr = $('.da-transcript');
	var trReturn = null;

	function openTr() {
		if (!tr) { return; }
		trReturn = document.activeElement;
		tr.hidden = false;
		var h = $('#da-tr-title', tr);
		if (h) { h.focus(); }
	}
	function closeTr() {
		if (!tr || tr.hidden) { return; }
		tr.hidden = true;
		if (trReturn && trReturn.focus) { trReturn.focus(); }
	}
	if (tr) {
		$('.da-tr-close', tr).addEventListener('click', closeTr);
		tr.addEventListener('click', function (e) { if (e.target === tr) { closeTr(); } });
	}
	function trapFocus(e, box) {
		var f = $$('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])', box).filter(visible);
		if (!f.length) { return; }
		var first = f[0], last = f[f.length - 1];
		if (e.shiftKey && (document.activeElement === first || !box.contains(document.activeElement))) {
			e.preventDefault(); last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault(); first.focus();
		}
	}

	/* ---------- Teclado ---------- */
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' || e.key === 'Esc') {
			if (tr && !tr.hidden) { closeTr(); return; }
			if (!panel.hidden) { closePanel(); return; }
			if (lsc && !lsc.hidden) { closeLsc(); return; }
			if (ttsOK && synth.speaking) { synth.cancel(); announce(T.stopped); }
			return;
		}
		if (e.key === 'Tab' && tr && !tr.hidden) { trapFocus(e, tr); return; }
		if (C.shortcut && e.altKey && !e.ctrlKey && !e.metaKey && e.code === 'KeyA') {
			e.preventDefault();
			togglePanel();
		}
	});

	/* ---------- Acciones del panel ---------- */
	root.addEventListener('click', function (e) {
		var b = e.target.closest ? e.target.closest('button') : null;
		if (!b || !root.contains(b)) { return; }

		var toggle = b.getAttribute('data-da-toggle');
		var contrast = b.getAttribute('data-da-contrast');
		var action = b.getAttribute('data-da-action');

		if (toggle) {
			prefs[toggle] = !prefs[toggle];
			commit(false);
			if (toggle === 'captions') { applyCaptions(true); return; }
			if (toggle === 'motion') { applyMotion(); }
			if (toggle === 'magnifier' && !prefs.magnifier && mag) { mag.classList.remove('is-on'); }
			if (toggle === 'clickread' && !prefs.clickread && ttsOK) { synth.cancel(); }
			announce(clean(b.textContent).replace(/^✓\s*/, '') + ': ' + (prefs[toggle] ? T.on : T.off));
			return;
		}

		if (contrast) {
			prefs.contrast = contrast;
			commit(false);
			announce(fmt(T.contrast, clean(b.textContent)));
			return;
		}

		switch (action) {
			case 'scale-up':
			case 'scale-down':
				prefs.scale = Math.max(MIN, Math.min(MAX, prefs.scale + (action === 'scale-up' ? 1 : -1)));
				commit(true);
				announce(fmt(T.scale, Math.round((1 + prefs.scale * STEP) * 100) + '%'));
				break;
			case 'read-page':
				if (speak(readableText())) { announce(T.reading); }
				break;
			case 'read-pause':
				if (!ttsOK) { break; }
				if (synth.paused) { synth.resume(); announce(T.resumed); }
				else if (synth.speaking) { synth.pause(); announce(T.paused); }
				break;
			case 'read-stop':
				if (ttsOK) { synth.cancel(); announce(T.stopped); }
				break;
			case 'lsc-open':
				if (lsc && !lsc.hidden) { closeLsc(); } else { openLsc(); }
				break;
			case 'transcript-open':
				openTr();
				break;
			case 'reset':
				if (ttsOK) { synth.cancel(); }
				closeLsc();
				var hadCaptions = prefs.captions;
				prefs = defaults();
				commit(true);
				if (hadCaptions) { applyCaptions(false); }
				if (mag) { mag.classList.remove('is-on'); }
				announce(T.reset);
				break;
		}
	});

	var rateSelect = $('#da-rate');
	if (rateSelect) {
		rateSelect.addEventListener('change', function () {
			prefs.rate = parseFloat(rateSelect.value) || 1;
			save();
		});
	}

	/* ---------- Inicio ---------- */
	applyClasses();
	applyScale();
	syncUI();
	applyMotion();
	if (prefs.captions) { applyCaptions(false); }

	// Constructores como Elementor terminan de pintar tras "load": se reaplica lo que depende del DOM.
	window.addEventListener('load', function () {
		if (prefs.scale) { applyScale(); }
		if (prefs.captions) { applyCaptions(false); }
		applyMotion();
	});
})();
