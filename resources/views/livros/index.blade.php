<x-guest-layout>
	<x-slot name="heading">
		📚 Livros
	</x-slot>

	<div class="w-full mx-auto px-16">
		<div class="bg-white shadow rounded-lg w-full overflow-x-auto pt-8">

			{{-- FORMULÁRIO DE FILTRO E EXPORTAÇÃO --}}
			<form method="GET" action="{{ route('livros.export') }}" id="export-form" class="flex flex-col items-center gap-4 mb-6">
				<input type="hidden" name="ids" id="selected-ids" />

				<div class="flex flex-col sm:flex-row gap-4 w-full justify-center items-end">
					<div class="self-end">
						<button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
					</div>

					{{-- FILTRO POR AUTOR --}}
					<div class="form-control w-full max-w-xs">
						<select name="autor_id" class="select select-neutral select-bordered w-full bg-white text-gray-900">
							<option value="">Todos os Autores</option>
							@foreach ($autores as $autor)
							<option value="{{ $autor->id }}" @selected(request('autor_id')==$autor->id)>{{ $autor->nome }}</option>
							@endforeach
						</select>
					</div>

					{{-- FILTRO POR EDITORA --}}
					<div class="form-control w-full max-w-xs">
						<select name="editora_id" class="select select-neutral select-bordered w-full bg-white text-gray-900">
							<option value="">Todas as Editoras</option>
							@foreach ($editoras as $editora)
							<option value="{{ $editora->id }}" @selected(request('editora_id')==$editora->id)>{{ $editora->nome }}</option>
							@endforeach
						</select>
					</div>

					{{-- BOTÕES --}}
					<div class="flex gap-2 self-end">
						<button type="submit" formaction="{{ route('livros.index') }}" class="btn btn-outline btn-info">Filtrar</button>
						<a href="{{ route('livros.index') }}" class="btn btn-outline btn-error">Limpar</a>
						@auth
						@if (Auth::user()->isAdmin())
						<label for="modal-add-livro" class="btn btn-primary ml-10"><i class="fa-solid fa-circle-plus"></i> Livro</label>
						@endif
						@endauth
					</div>
				</div>
			</form>

			@php
			function sort_link($field, $label, $currentField, $currentDirection) {
				$newDirection = ($currentField === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
				$icon = $currentField === $field ? ($currentDirection === 'asc' ? '▲' : '▼') : '';
				$query = request()->except(['page', 'sort', 'direction']);
				$query['sort'] = $field;
				$query['direction'] = $newDirection;
				$url = route('livros.index', $query);
				return "<a href='{$url}' class='hover:underline font-medium'>{$label} {$icon}</a>";
			}
			@endphp

			<div class="overflow-x-auto w-full">
				<table class="table w-full">
					<thead class="bg-gray-700 text-white font-semibold">
						<tr>
							@auth
							<th class="w-10 px-2"><input id="checkAll" type="checkbox" class="checkbox checkbox-primary" /></th>
							@endauth
							<th class="min-w-[80px]">Capa</th>
							<th class="min-w-[150px]">{!! sort_link('nome', 'Título', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[100px]">{!! sort_link('isbn', 'ISBN', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[150px]">Autores</th>
							<th class="min-w-[120px]">{!! sort_link('editora_id', 'Editora', $sortField, $sortDirection) !!}</th>
							<th class="min-w-[80px]">{!! sort_link('preco', 'Preço', $sortField, $sortDirection) !!}</th>
							<th>Disponível?</th>
							@auth
								<th class="min-w-[120px] text-center">Comprar</th>
							@endauth
							@auth
							@if (Auth::user()->isAdmin())
								<th class="w-32 text-center">Ações</th>
							@endif
							@endauth
						</tr>
					</thead>
					<tbody>
						@foreach($livros as $livro)
						<tr class="hover text-neutral odd:bg-gray-100 even:bg-white" data-id="{{ $livro->id }}">
							@auth
							<td class="px-2"><input type="checkbox" class="checkbox checkbox-primary row-checkbox" data-id="{{ $livro->id }}" /></td>
							@endauth

							<td onclick='mostrarLivro(@json($livro))' class="cursor-pointer">
								<img src="{{ $livro->imagem_capa }}" class="w-[75px] h-[100px] object-cover shadow rounded" />
							</td>

							<td onclick='mostrarLivro(@json($livro))' class="cursor-pointer hover:underline text-blue-600 font-medium">
								{{ $livro->nome }}
							</td>

							<td>{{ $livro->isbn }}</td>

							<td class="max-w-[150px]">
								@foreach ($livro->autores as $autor)
								<span class="text-sm">{{ $autor->nome }}</span>
								@if (!$loop->last)<br>@endif
								@endforeach
							</td>

							<td>{{ $livro->editora?->nome }}</td>
							<td>€ {{ number_format($livro->preco, 2, ',', '.') }}</td>

							<td>
								@if($livro->disponivel)
								<span class="badge badge-success">Sim</span>
								@else
								<span class="badge badge-error">Não</span>
								@endif
							</td>
							@auth
							<td class="text-center">
								<form method="POST" action="{{ route('carrinho.add', ['livro' => $livro->getRouteKey()]) }}">
									@csrf
									<input type="hidden" name="quantidade" value="1">
									<button class="btn btn-sm btn-outline btn-success">+ 🛒</button>
								</form>
							</td>
							@endauth
							
							@auth
							@if(Auth::user()->isAdmin())
							<td class="w-32 px-2">
								<div class="flex justify-center space-x-1">
									<a href="{{ route('livros.index', array_merge(request()->query(), ['edit' => $livro->id])) }}" class="btn btn-sm btn-ghost hover:bg-gray-100">✏️</a>
									<form method="POST" action="{{ route('livros.destroy', $livro->id) }}" class="inline">
										@csrf
										@method('DELETE')
										<button type="submit" onclick="return confirm('Deseja excluir este livro?')" class="btn btn-sm btn-ghost hover:bg-gray-100">🗑️</button>
									</form>
								</div>
							</td>
							@endif
							@endauth
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	{{-- MODAL: Detalhes do Livro --}}
	<input type="checkbox" id="modal-detalhes-livro" class="modal-toggle" />
	<div class="modal">
		<div class="modal-box w-11/12 max-w-4xl text-left" data-theme="light">
			{{-- Cabeçalho --}}
			<div class="flex flex-col md:flex-row gap-6">
				<img id="modal-livro-capa" src="" class="w-[200px] h-[240px] rounded shadow" />
				<div class="flex flex-col gap-2 text-sm">
					<h3 id="modal-livro-nome" class="text-2xl font-bold text-indigo-700 mb-1"></h3>
					<p><strong class="text-gray-700">ISBN:</strong> <span id="modal-livro-isbn"></span></p>
					<p><strong class="text-gray-700">Editora:</strong> <span id="modal-livro-editora"></span></p>
					<p><strong class="text-gray-700">Autores:</strong> <span id="modal-livro-autores"></span></p>
					<p><strong class="text-gray-700">Preço:</strong> €<span id="modal-livro-preco"></span></p>
					<p><strong>Disponível:</strong> <span id="modal-livro-disponivel"></span></p>
					<p><strong class="text-gray-700">Bibliografia:</strong></p>
					<p id="modal-livro-bibliografia" class="whitespace-pre-wrap text-justify text-gray-800"></p>
				</div>
			</div>

			{{-- Botões --}}
			<div class="mt-8 flex justify-center gap-4">
				<button type="button" class="btn btn-neutral btn-outline" onclick="toggleHistorico()">Histórico</button>
				<button type="button" class="btn btn-neutral btn-outline" onclick="toggleReviews()">Reviews</button>

				<form id="form-requisitar-livro" method="GET" action="{{ route('requisicoes.create') }}">
					<input type="hidden" name="livro_id" id="input-livro-id" />
					<button type="submit" class="btn btn-primary btn-outline">Solicitar</button>
				</form>

				<form id="form-alerta-disponibilidade" method="POST" action="{{ route('livros.alertar-disponibilidade') }}">
					@csrf
					<input type="hidden" name="livro_id" id="input-livro-id-alerta" />
					<button type="submit" class="btn btn-warning btn-outline">Avise-me</button>
				</form>

				@auth
				<form id="form-add-carrinho" method="POST" action="#">
					@csrf
					<input type="hidden" name="quantidade" value="1">
					<button class="btn btn-outline btn-success">+ Carrinho</button>
				</form>
				@endauth

				<label for="modal-detalhes-livro" class="btn btn-error btn-outline">Fechar</label>
			</div>

			{{-- Histórico --}}
			<div id="wrap-historico" class="mt-10 hidden">
				<h4 class="text-lg font-semibold mb-2">📖 Histórico de Requisições</h4>
				<div id="historico-requisicoes" class="overflow-x-auto bg-gray-50 p-4 rounded shadow-inner">
					<p class="text-gray-500 italic">Clique em "Histórico" para exibir.</p>
				</div>
			</div>

			{{-- Reviews --}}
			<div id="wrap-reviews" class="mt-10 hidden">
				<h4 class="text-lg font-semibold mb-2">⭐ Reviews</h4>
				<div id="reviews-container" class="overflow-x-auto bg-gray-50 p-4 rounded shadow-inner">
					<p class="text-gray-500 italic">Clique em "Reviews" para exibir.</p>
				</div>
			</div>

			{{-- Relacionados --}}
			<section id="wrap-relacionados" class="mt-10" aria-labelledby="relacionados-title">
				<h4 id="relacionados-title" class="text-lg font-semibold mb-2">📚 Livros relacionados</h4>
				<div id="relacionados-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
					<div class="opacity-60">Carregando…</div>
				</div>
			</section>
		</div>
	</div>

	{{-- Injeta usuário logado no window (sem chamar /api/user) --}}
	@auth
	<script>
	  // serializado com segurança para JS
	  window.AUTH_ID = @json(auth()->id());
	  window.AUTH_ROLE = @json(optional(auth()->user())->role ?? optional(auth()->user())->tipo);
	  window.AUTH_IS_ADMIN = @json((optional(auth()->user())->role ?? optional(auth()->user())->tipo) === 'admin');
	</script>
	@else
	<script>
	  window.AUTH_ID = null;
	  window.AUTH_ROLE = null;
	  window.AUTH_IS_ADMIN = false;
	</script>
	@endauth


	{{-- SCRIPTS PRINCIPAIS --}}
	<script>
		// ====== Estado atual do modal ======
		let LIVRO_ATUAL = null;

		// ====== Abrir modal e preencher campos ======
		function normalizarLivro(data) {
			if (!data || typeof data !== 'object') return {};

			let editoraObj = null;
			if (data.editora && typeof data.editora === 'object') {
				editoraObj = data.editora;
			} else if (typeof data.editora === 'string') {
				editoraObj = { nome: data.editora };
			}

			let autoresArr = [];
			if (Array.isArray(data.autores)) {
				autoresArr = data.autores.map(a => {
					if (typeof a === 'string') return { id: null, nome: a };
					return { id: a.id ?? null, nome: a.nome ?? '' };
				});
			}

			return {
				id: data.id ?? null,
				nome: data.nome ?? '',
				isbn: data.isbn ?? '',
				editora: editoraObj,
				autores: autoresArr,
				preco: (data.preco ?? 0),
				disponivel: !!data.disponivel,
				bibliografia: data.bibliografia ?? '',
				imagem_capa: data.imagem_capa ?? '',
				requisicoes: Array.isArray(data.requisicoes) ? data.requisicoes : [],
			};
		}

		// ===== Principal: abre/preenche modal e carrega relacionados =====
		async function mostrarLivro(livroRaw) {
			const livro = normalizarLivro(livroRaw);

			const autoresTxt = livro.autores.map(a => a.nome).join(', ');
			document.getElementById('modal-livro-nome').innerText = livro.nome || '—';
			document.getElementById('modal-livro-isbn').innerText = livro.isbn || 'N/A';
			document.getElementById('modal-livro-editora').innerText = livro.editora?.nome || 'N/A';
			document.getElementById('modal-livro-autores').innerText = autoresTxt || '—';
			document.getElementById('modal-livro-preco').innerText = (Number(livro.preco) || 0).toFixed(2);
			document.getElementById('modal-livro-disponivel').innerText = livro.disponivel ? 'Sim' : 'Não';
			document.getElementById('modal-livro-bibliografia').innerText = livro.bibliografia || '';
			document.getElementById('modal-livro-capa').src = livro.imagem_capa || '';

			const inputLivroId = document.getElementById('input-livro-id');
			if (inputLivroId) inputLivroId.value = livro.id;

			const formRequisitar = document.getElementById('form-requisitar-livro');
			if (formRequisitar) {
				if (livro.disponivel) formRequisitar.classList.remove('hidden');
				else formRequisitar.classList.add('hidden');
			}

			const formAlerta = document.getElementById('form-alerta-disponibilidade');
			const inputAlerta = document.getElementById('input-livro-id-alerta');
			if (formAlerta) {
				if (livro.disponivel) {
					formAlerta.classList.add('hidden');
				} else {
					formAlerta.classList.remove('hidden');
					if (inputAlerta) inputAlerta.value = livro.id;
				}
			}

			LIVRO_ATUAL = livro;
			const formAdd = document.getElementById('form-add-carrinho');
			if (formAdd && livro.id) {
				formAdd.action = "{{ url('/livros') }}/" + livro.id + "/add";
			}

			document.getElementById('wrap-historico')?.classList.add('hidden');
			document.getElementById('wrap-reviews')?.classList.add('hidden');

			const historicoContainer = document.getElementById('historico-requisicoes');
			if (historicoContainer) {
				historicoContainer.innerHTML = `<p class="text-gray-500 italic">Clique em "Histórico" para exibir.</p>`;
				historicoContainer.dataset.filled = '0';
			}

			const reviewsContainer = document.getElementById('reviews-container');
			if (reviewsContainer) {
				reviewsContainer.innerHTML = `<p class="text-gray-500 italic">Clique em "Reviews" para exibir.</p>`;
				reviewsContainer.dataset.filled = '0';
			}

			document.getElementById('modal-detalhes-livro').checked = true;
			document.getElementById('input-livro-id').value = livro.id;
			sessionStorage.setItem('livro_id_requisicao', livro.id);

			const target = document.getElementById('relacionados-container');
			if (!target) return;

			target.innerHTML = `<div class="opacity-60">Carregando…</div>`;
			try {
				const res = await fetch(`/livros/${livro.id}/relacionados`, {
					headers: { 'Accept': 'application/json' }
				});
				const items = res.ok ? await res.json() : [];

				if (!items.length) {
					target.innerHTML = `<div class="opacity-60">Sem sugestões no momento.</div>`;
					return;
				}

				target.innerHTML = items.map((it, idx) => `
				<div class="card bg-base-100 shadow hover:shadow-lg transition cursor-pointer" data-idx="${idx}">
					<figure class="p-3">
						${it.imagem_capa
							? `<img src="${it.imagem_capa}" alt="${it.nome}" class="h-40 w-auto object-contain">`
							: `<div class="h-40 w-full flex items-center justify-center bg-base-200 text-base-content/60">
									<i class="fas fa-book text-4xl"></i>
							   </div>`}
					</figure>
					<div class="card-body p-4">
						<h3 class="card-title text-sm line-clamp-2">${it.nome}</h3>
						${it.editora ? `<p class="text-xs text-base-content/70">${it.editora}</p>` : ''}
						<div class="card-actions justify-end mt-2">
							<span class="btn btn-ghost btn-xs">Abrir detalhes</span>
						</div>
					</div>
				</div>
			`).join('');

				target.querySelectorAll('.card[data-idx]').forEach(card => {
					card.addEventListener('click', () => {
						const idx = Number(card.dataset.idx);
						const item = items[idx];
						mostrarLivro(item);
					});
				});
			} catch (e) {
				target.innerHTML = `<div class="alert alert-error">Erro ao carregar relacionados.</div>`;
			}
		}

		// ====== Toggles ======
		function toggleHistorico() {
			const wHist = document.getElementById('wrap-historico');
			const wRev = document.getElementById('wrap-reviews');

			if (wHist.classList.contains('hidden')) {
				wHist.classList.remove('hidden');
				wRev.classList.add('hidden');
				renderHistoricoSePreciso();
			} else {
				wHist.classList.add('hidden');
			}
		}

		function toggleReviews() {
			const wHist = document.getElementById('wrap-historico');
			const wRev = document.getElementById('wrap-reviews');

			if (wRev.classList.contains('hidden')) {
				wRev.classList.remove('hidden');
				wHist.classList.add('hidden');
				renderReviewsSePreciso();
			} else {
				wRev.classList.add('hidden');
			}
		}

		// ====== Render Histórico ======
		function renderHistoricoSePreciso() {
			if (!LIVRO_ATUAL) return;
			const c = document.getElementById('historico-requisicoes');
			if (!c || c.dataset.filled === '1') return;

			const reqs = Array.isArray(LIVRO_ATUAL.requisicoes) ? LIVRO_ATUAL.requisicoes : [];
			if (reqs.length === 0) {
				c.innerHTML = `<p class="text-gray-500 italic">Nenhuma requisição registrada.</p>`;
				c.dataset.filled = '1';
				return;
			}

			const rows = reqs.map(req => `
				<tr>
					<td>${req.numero ?? ('REQ-' + String(req.id ?? '').padStart(4,'0'))}</td>
					<td>${req.user?.name ?? 'Desconhecido'}</td>
					<td>${req.data_inicio ?? '-'}</td>
					<td>${req.data_fim_prevista ?? '-'}</td>
					<td>${req.data_fim_real ?? '<span class="italic text-gray-400">Pendente</span>'}</td>
					<td>
						<span class="badge ${req.status === 'ativa' ? 'badge-success' : (req.status === 'devolvida' ? 'badge-info' : 'badge-warning')}">
							${String(req.status ?? '').charAt(0).toUpperCase() + String(req.status ?? '').slice(1)}
						</span>
					</td>
				</tr>
			`).join('');

			c.innerHTML = `
				<table class="table table-zebra w-full text-sm">
					<thead class="bg-gray-100">
						<tr>
							<th>#</th>
							<th>Cidadão</th>
							<th>Início</th>
							<th>Previsão Fim</th>
							<th>Fim Real</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>${rows}</tbody>
				</table>
			`;
			c.dataset.filled = '1';
		}

		// ====== Render Reviews ======
		async function renderReviewsSePreciso() {
			const c = document.getElementById('reviews-container');
			if (!c || !LIVRO_ATUAL) return;

			function escapeHtml(unsafe) {
				return unsafe?.toString()
					.replace(/&/g, "&amp;").replace(/</g, "&lt;")
					.replace(/>/g, "&gt;").replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;") || '';
			}

			c.innerHTML = `<div class="opacity-60">Carregando reviews...</div>`;
			c.dataset.filled = '0';

			const livroId = LIVRO_ATUAL.id;
			if (!livroId) {
				c.innerHTML = `<div class="alert alert-error">ID do livro inválido.</div>`;
				c.dataset.filled = '1';
				return;
			}

			try {
				// 1) Buscar reviews ativas
				const res = await fetch(`/livros/${livroId}/reviews?t=${Date.now()}`, {
					headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
				});
				if (!res.ok) {
					c.innerHTML = `<div class="alert alert-error">Falha ao carregar reviews (HTTP ${res.status})</div>`;
					c.dataset.filled = '1';
					return;
				}
				const reviews = await res.json();

				// 2) Está logado?
				const ehLogado = (window.AUTH_ID != null); // <— alterado para ser robusto

				// 3) Buscar MINHAS devoluções desse livro (se logado)
				let devolvidasDoUsuario = [];
				if (ehLogado) {
					try {
						const respMinhas = await fetch(`/livros/${livroId}/minhas-devolucoes?t=${Date.now()}`, {
							headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
							credentials: 'same-origin'
						});
						if (respMinhas.ok) {
							const ct = respMinhas.headers.get('content-type') || '';
							if (ct.includes('application/json')) {
								devolvidasDoUsuario = await respMinhas.json();
							} else {
								console.warn('minhas-devolucoes não retornou JSON (provável redirect).');
							}
						} else if (respMinhas.status === 401) {
							console.warn('Não autenticado para listar devoluções.');
						} else {
							console.warn('Falha ao buscar minhas devoluções:', respMinhas.status);
						}
					} catch (e) {
						console.warn('Erro ao buscar minhas devoluções:', e);
					}
				}

				const podeEnviarReview = ehLogado && Array.isArray(devolvidasDoUsuario) && devolvidasDoUsuario.length > 0;

				// 4) Lista de reviews
				const listHtml = reviews.length > 0 ?
					reviews.map(r => `
          <div class="border rounded p-4 bg-base-200 mb-3">
            <div class="flex items-center justify-between">
              <div class="font-semibold">${escapeHtml(r.user?.name || 'Anônimo')}</div>
              <div class="text-sm opacity-70">${new Date(r.created_at).toLocaleDateString()}</div>
            </div>
            <div class="mt-1">${'⭐'.repeat(r.rating)}</div>
            ${r.comentario ? `<p class="mt-2">${escapeHtml(r.comentario)}</p>` : ''}
          </div>
        `).join('') :
					`<div class="opacity-60">Ainda não há reviews para este livro.</div>`;

				// 5) Formulário (1 ou várias devoluções)
				let formHtml = '';
				if (podeEnviarReview) {
					if (devolvidasDoUsuario.length === 1) {
						const requisicaoId = devolvidasDoUsuario[0].id;
						formHtml = `
          <div class="mt-6 border rounded p-4 bg-white">
            <h4 class="font-semibold mb-3">Deixar uma review</h4>
            <form id="form-review" method="POST" action="/requisicoes/${requisicaoId}/reviews">
              <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
              <div class="flex items-center gap-3 mb-3">
                <label class="label">Avaliação:</label>
                <select name="rating" class="select select-bordered" required>
                  <option value="">Selecione</option>
                  ${[1,2,3,4,5].map(i => `<option value="${i}">${i} ⭐</option>`).join('')}
                </select>
              </div>
              <div class="mb-3">
                <label class="label">Comentário (opcional)</label>
                <textarea name="comentario" class="textarea textarea-bordered w-full" rows="4"
                  maxlength="2000" placeholder="O que achou do livro?"></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Enviar review</button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Sua review será analisada antes de ser publicada.</p>
          </div>`;
					} else {
						const options = devolvidasDoUsuario.map(req => {
							const numero = req.numero ?? ('REQ-' + String(req.id).padStart(4, '0'));
							const fim = req.data_fim_real ? new Date(req.data_fim_real).toLocaleDateString() : '-';
							return `<option value="${req.id}">${numero} — devolvido em ${fim}</option>`;
						}).join('');

						formHtml = `
          <div class="mt-6 border rounded p-4 bg-white">
            <h4 class="font-semibold mb-3">Deixar uma review</h4>
            <form id="form-review" method="POST">
              <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
              <div class="mb-3">
                <label class="label">Escolha a requisição que deseja avaliar</label>
                <select id="requisicaoParaReview" class="select select-bordered" required>
                  <option value="">Selecione...</option>
                  ${options}
                </select>
              </div>
              <div class="flex items-center gap-3 mb-3">
                <label class="label">Avaliação:</label>
                <select name="rating" class="select select-bordered" required>
                  <option value="">Selecione</option>
                  ${[1,2,3,4,5].map(i => `<option value="${i}">${i} ⭐</option>`).join('')}
                </select>
              </div>
              <div class="mb-3">
                <label class="label">Comentário (opcional)</label>
                <textarea name="comentario" class="textarea textarea-bordered w-full" rows="4"
                  maxlength="2000" placeholder="O que achou do livro?"></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Enviar review</button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Sua review será analisada antes de ser publicada.</p>
          </div>`;
					}
				}

				c.innerHTML = `<h5 class="font-semibold mb-2">Reviews</h5>${listHtml}${formHtml}`;
				c.dataset.filled = '1';

				// 6) Submit do form
				const form = document.getElementById('form-review');
				if (form) {
					form.addEventListener('submit', async (e) => {
						e.preventDefault();
						let action = form.getAttribute('action');
						if (!action) {
							const reqSel = document.getElementById('requisicaoParaReview');
							if (!reqSel?.value) {
								alert('Selecione a requisição.');
								return;
							}
							action = `/requisicoes/${reqSel.value}/reviews`;
						}
						const formData = new FormData(form);
						const response = await fetch(action, {
							method: 'POST',
							headers: {
								'Accept': 'application/json',
								'X-Requested-With': 'XMLHttpRequest',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							},
							body: formData,
							credentials: 'same-origin'
						});
						if (response.ok) {
							alert('Review enviada com sucesso! Ela será analisada antes de ser publicada.');
							renderReviewsSePreciso();
						} else {
							const err = await response.json().catch(() => ({}));
							alert(`Erro: ${err.message ?? 'Não foi possível enviar a review.'}`);
						}
					});
				}
			} catch (err) {
				console.error('Erro ao carregar reviews:', err);
				c.innerHTML = `<div class="alert alert-error">Falha ao carregar reviews.</div>`;
				c.dataset.filled = '1';
			}
		}

		// ====== Check All + Export ======
		document.addEventListener('DOMContentLoaded', function() {
			const checkAll = document.getElementById('checkAll');
			const rowCheckboxes = document.querySelectorAll('.row-checkbox');
			const exportForm = document.getElementById('export-form');
			const selectedIdsInput = document.getElementById('selected-ids');

			if (checkAll) {
				checkAll.addEventListener('change', function() {
					rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
				});
			}

			if (exportForm) {
				exportForm.addEventListener('submit', function() {
					const selected = Array.from(rowCheckboxes)
						.filter(cb => cb.checked)
						.map(cb => cb.closest('tr').dataset.id);

					selectedIdsInput.value = selected.join(',');
				});
			}
		});
	</script>

</x-guest-layout>
