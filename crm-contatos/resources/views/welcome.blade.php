<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Mini CRM - Dashboard de Contatos</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        .contact-row-updated {
            animation: highlight 2s ease-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.5); }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div id="app" class="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mini CRM - Contatos</h1>
                <p class="mt-2 text-sm text-gray-600">Acompanhamento de Score em Tempo Real (Reverb)</p>
            </div>
            <div class="flex items-center space-x-2">
                <span id="connection-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <span class="w-2 h-2 mr-1.5 bg-red-400 rounded-full"></span>
                    Desconectado
                </span>
            </div>
        </header>

        <main>
            <section class="mb-8 bg-white shadow rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Novo contato</h2>
                        <p class="mt-1 text-sm text-gray-500">Adicione contato rápido e acompanhe o score em tempo real.</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Campos obrigatórios: <span class="font-medium">name</span>, <span class="font-medium">email</span>, <span class="font-medium">phone</span>
                    </div>
                </div>

                <form id="contact-form" class="mt-6 grid gap-4 sm:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Nome</span>
                        <input id="contact-name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Bruno Melo" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Email</span>
                        <input id="contact-email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="bruno@example.com" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Telefone</span>
                        <input id="contact-phone" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="11999999999" />
                    </label>
                    <div class="sm:col-span-3 text-right">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Criar contato</button>
                    </div>
                </form>
            </section>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="contacts-table-body" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">Carregando contatos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="mt-8 text-center text-xs text-gray-500">
            Mini CRM Contatos &copy; 2026 - Laravel 11 + Reverb + DDD
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusBadge = document.getElementById('connection-status');
            const tableBody = document.getElementById('contacts-table-body');
            const contactForm = document.getElementById('contact-form');
            const nameInput = document.getElementById('contact-name');
            const emailInput = document.getElementById('contact-email');
            const phoneInput = document.getElementById('contact-phone');

            let contacts = [];
            const subscribedContactIds = new Set();

            contactForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                await createContact();
            });

            // 1. Monitorar Conexão do Echo (Reverb)
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    statusBadge.innerHTML = '<span class="w-2 h-2 mr-1.5 bg-green-400 rounded-full"></span> Conectado';
                    statusBadge.classList.replace('bg-red-100', 'bg-green-100');
                    statusBadge.classList.replace('text-red-800', 'text-green-800');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    statusBadge.innerHTML = '<span class="w-2 h-2 mr-1.5 bg-red-400 rounded-full"></span> Desconectado';
                    statusBadge.classList.replace('bg-green-100', 'bg-red-100');
                    statusBadge.classList.replace('text-green-800', 'text-red-800');
                });
            }

            async function fetchContacts() {
                try {
                    const response = await fetch('/api/contacts');
                    const result = await response.json();
                    contacts = result.data;
                    renderTable(contacts);
                    contacts.forEach(contact => subscribeToContact(contact.id));
                } catch (error) {
                    console.error('Erro ao buscar contatos:', error);
                    tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-red-500">Erro ao carregar contatos.</td></tr>';
                }
            }

            function renderTable(items) {
                if (items.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Nenhum contato encontrado. Crie um contato acima para começar.</td></tr>';
                    return;
                }

                tableBody.innerHTML = items.map(contact => createRowHtml(contact)).join('');
            }

            function createRowHtml(contact) {
                const statusColors = {
                    'pending': 'bg-gray-100 text-gray-800',
                    'processing': 'bg-blue-100 text-blue-800 animate-pulse',
                    'active': 'bg-green-100 text-green-800',
                    'failed': 'bg-red-100 text-red-800'
                };

                return `
                    <tr id="contact-row-${contact.id}" class="transition-colors duration-500">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${contact.name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${contact.email}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${contact.phone}</td>
                        <td id="contact-score-${contact.id}" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${contact.score}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span id="contact-status-${contact.id}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[contact.status]}">
                                ${contact.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button onclick="processScore(${contact.id})" class="text-indigo-600 hover:text-indigo-900">Processar</button>
                            <button onclick="editContact(${contact.id})" class="text-amber-600 hover:text-amber-900">Editar</button>
                            <button onclick="deleteContact(${contact.id})" class="text-red-600 hover:text-red-900">Excluir</button>
                        </td>
                    </tr>
                `;
            }

            function subscribeToContact(contactId) {
                if (!window.Echo || subscribedContactIds.has(contactId)) {
                    return;
                }

                subscribedContactIds.add(contactId);
                window.Echo.channel(`contacts.${contactId}`)
                    .listen('.contact.score.processed', (e) => {
                        updateRow(e.contact);
                    });
            }

            function updateRow(contact) {
                const scoreEl = document.getElementById(`contact-score-${contact.id}`);
                const statusEl = document.getElementById(`contact-status-${contact.id}`);
                const rowEl = document.getElementById(`contact-row-${contact.id}`);

                if (scoreEl) scoreEl.innerText = contact.score;
                if (statusEl) {
                    statusEl.innerText = contact.status;
                    statusEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                }
                if (rowEl) {
                    rowEl.classList.add('contact-row-updated');
                    setTimeout(() => rowEl.classList.remove('contact-row-updated'), 2000);
                }
            }

            async function createContact() {
                const payload = {
                    name: nameInput.value.trim(),
                    email: emailInput.value.trim(),
                    phone: phoneInput.value.trim()
                };

                if (!payload.name || !payload.email || !payload.phone) {
                    alert('Preencha todos os campos do contato.');
                    return;
                }

                try {
                    const response = await fetch('/api/contacts', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        throw new Error('Não foi possível criar o contato.');
                    }

                    nameInput.value = '';
                    emailInput.value = '';
                    phoneInput.value = '';
                    await fetchContacts();
                } catch (error) {
                    console.error('Erro ao criar contato:', error);
                    alert('Erro ao criar contato. Verifique os dados e tente novamente.');
                }
            }

            window.editContact = async (id) => {
                const contact = contacts.find(item => item.id === id);
                if (!contact) return;

                const name = prompt('Nome do contato:', contact.name);
                if (name === null) return;

                const email = prompt('Email do contato:', contact.email);
                if (email === null) return;

                const phone = prompt('Telefone do contato:', contact.phone);
                if (phone === null) return;

                try {
                    const response = await fetch(`/api/contacts/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: name.trim(), email: email.trim(), phone: phone.trim() })
                    });

                    if (!response.ok) {
                        throw new Error('Não foi possível atualizar o contato.');
                    }

                    await fetchContacts();
                } catch (error) {
                    console.error('Erro ao atualizar contato:', error);
                    alert('Erro ao atualizar contato. Tente novamente.');
                }
            };

            window.deleteContact = async (id) => {
                if (!confirm('Tem certeza que deseja excluir este contato?')) {
                    return;
                }

                try {
                    const response = await fetch(`/api/contacts/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Não foi possível excluir o contato.');
                    }

                    await fetchContacts();
                } catch (error) {
                    console.error('Erro ao excluir contato:', error);
                    alert('Erro ao excluir contato. Tente novamente.');
                }
            };

            window.processScore = async (id) => {
                const statusEl = document.getElementById(`contact-status-${id}`);
                if (statusEl) {
                    statusEl.innerText = 'processing';
                    statusEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse';
                }

                try {
                    await fetch(`/api/contacts/${id}/process-score`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                } catch (error) {
                    console.error('Erro ao processar score:', error);
                    alert('Erro ao iniciar o processamento.');
                }
            };

            fetchContacts();
        });
    </script>
</body>
</html>
