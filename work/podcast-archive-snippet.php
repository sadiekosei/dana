<?php
/**
 * Podcast episode search + browse archive.
 * Adds a searchable, filterable grid of all "podcast" CPT episodes
 * beneath the existing "Latest Episodes" section on the /podcast/ page
 * (post ID 3048). Renders via wp_footer so it works regardless of how
 * the page itself is built (Elementor stores its layout separately
 * from post_content, so this can't be added by editing the page).
 *
 * Data is queried server-side and embedded as JSON; filtering/search/
 * pagination all run client-side in vanilla JS (no dependencies,
 * ~176 episodes is small enough that this is instant).
 */
add_action( 'wp_footer', function () {
	if ( ! is_page( 3048 ) ) {
		return;
	}

	$posts = get_posts( array(
		'post_type'      => 'podcast',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	$episodes = array();
	foreach ( $posts as $p ) {
		$tags = wp_get_post_tags( $p->ID, array( 'fields' => 'names' ) );
		$episodes[] = array(
			'title'   => get_the_title( $p ),
			'link'    => get_permalink( $p ),
			'excerpt' => wp_strip_all_tags( get_the_excerpt( $p ) ),
			'date'    => get_the_date( 'M j, Y', $p ),
			'ts'      => get_the_date( 'U', $p ),
			'thumb'   => get_the_post_thumbnail_url( $p, 'medium' ),
			'tags'    => $tags,
		);
	}

	// Only surface topic pills for tags that are clean, single-topic,
	// and reasonably common -- the tag data has messy pipe-delimited
	// entries mixed in from past bulk-imports that aren't fit for a
	// filter UI.
	$topic_pills = array( 'Boundaries', 'Wellness', 'Personal Growth', 'Mental Health', 'Leadership', 'Healing', 'Trauma', 'Anxiety', 'Burnout' );

	$data_json = wp_json_encode( $episodes );
	?>
	<section id="kosei-podcast-archive" style="max-width:1100px;margin:60px auto;padding:0 20px;font-family:inherit;">
		<h2 style="text-align:center;margin-bottom:8px;">Browse All Episodes</h2>
		<p style="text-align:center;color:#666;margin-bottom:28px;">Search <?php echo count( $episodes ); ?> episodes by topic or guest name.</p>

		<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:center;margin-bottom:18px;">
			<input type="text" id="kpa-search" placeholder="Search episodes or guests&hellip;"
				style="flex:1 1 280px;max-width:420px;padding:12px 16px;border:1px solid #ccc;border-radius:8px;font-size:16px;">
			<select id="kpa-sort" style="padding:12px 14px;border:1px solid #ccc;border-radius:8px;font-size:15px;">
				<option value="newest">Newest first</option>
				<option value="oldest">Oldest first</option>
			</select>
		</div>

		<div id="kpa-pills" style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:30px;">
			<button class="kpa-pill kpa-pill-active" data-topic="">All topics</button>
			<?php foreach ( $topic_pills as $topic ) : ?>
				<button class="kpa-pill" data-topic="<?php echo esc_attr( $topic ); ?>"><?php echo esc_html( $topic ); ?></button>
			<?php endforeach; ?>
		</div>

		<p id="kpa-count" style="text-align:center;color:#888;font-size:14px;margin-bottom:18px;"></p>

		<div id="kpa-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:22px;"></div>

		<div style="text-align:center;margin-top:30px;">
			<button id="kpa-loadmore" style="padding:12px 28px;border:1px solid #333;background:#fff;border-radius:8px;cursor:pointer;font-size:15px;">Show more episodes</button>
		</div>
	</section>

	<style>
		.kpa-pill { padding:8px 16px; border:1px solid #ccc; background:#fff; border-radius:999px; cursor:pointer; font-size:14px; transition:all .15s; }
		.kpa-pill:hover { border-color:#87A93A; }
		.kpa-pill-active { background:#87A93A; color:#fff; border-color:#87A93A; }
		.kpa-card { border:1px solid #e5e5e5; border-radius:10px; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; transition:box-shadow .15s,transform .15s; }
		.kpa-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
		.kpa-card img { width:100%; aspect-ratio:16/9; object-fit:cover; background:#f0f0f0; }
		.kpa-card-body { padding:16px; flex:1; display:flex; flex-direction:column; }
		.kpa-card-date { font-size:12px; color:#999; margin-bottom:6px; }
		.kpa-card-title { font-size:16px; font-weight:600; margin:0 0 8px; line-height:1.3; }
		.kpa-card-excerpt { font-size:14px; color:#555; line-height:1.4; flex:1; }
		#kpa-search:focus, #kpa-sort:focus { outline:2px solid #87A93A; outline-offset:1px; }
	</style>

	<script>
	(function () {
		var EPISODES = <?php echo $data_json; ?>;
		var PAGE_SIZE = 12;
		var state = { query: '', topic: '', sort: 'newest', shown: PAGE_SIZE };

		function normalize(s) { return (s || '').toLowerCase(); }

		function filtered() {
			var q = normalize(state.query);
			var list = EPISODES.filter(function (e) {
				if (state.topic && e.tags.indexOf(state.topic) === -1) return false;
				if (!q) return true;
				return normalize(e.title).indexOf(q) !== -1 || normalize(e.excerpt).indexOf(q) !== -1;
			});
			list.sort(function (a, b) { return state.sort === 'newest' ? b.ts - a.ts : a.ts - b.ts; });
			return list;
		}

		function render() {
			var list = filtered();
			var grid = document.getElementById('kpa-grid');
			var shown = list.slice(0, state.shown);
			grid.innerHTML = shown.map(function (e) {
				var img = e.thumb ? '<img src="' + e.thumb + '" alt="" loading="lazy">' : '';
				return '<a class="kpa-card" href="' + e.link + '">' + img +
					'<div class="kpa-card-body"><div class="kpa-card-date">' + e.date + '</div>' +
					'<div class="kpa-card-title">' + e.title + '</div>' +
					'<div class="kpa-card-excerpt">' + e.excerpt + '</div></div></a>';
			}).join('');
			document.getElementById('kpa-count').textContent = list.length + (list.length === 1 ? ' episode' : ' episodes') + (state.query || state.topic ? ' found' : '');
			document.getElementById('kpa-loadmore').style.display = state.shown < list.length ? 'inline-block' : 'none';
		}

		document.getElementById('kpa-search').addEventListener('input', function (e) {
			state.query = e.target.value; state.shown = PAGE_SIZE; render();
		});
		document.getElementById('kpa-sort').addEventListener('change', function (e) {
			state.sort = e.target.value; render();
		});
		document.getElementById('kpa-loadmore').addEventListener('click', function () {
			state.shown += PAGE_SIZE; render();
		});
		document.querySelectorAll('.kpa-pill').forEach(function (btn) {
			btn.addEventListener('click', function () {
				document.querySelectorAll('.kpa-pill').forEach(function (b) { b.classList.remove('kpa-pill-active'); });
				btn.classList.add('kpa-pill-active');
				state.topic = btn.getAttribute('data-topic'); state.shown = PAGE_SIZE; render();
			});
		});

		render();
	})();
	</script>
	<?php
}, 20 );
