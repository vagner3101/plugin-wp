Resumo da Estrutura e Diretrizes de Desenvolvimento do Plugin Gráfica Rápida

Estrutura de Diretórios:
grafica-rapida-plugin/

├── assets/

│ ├── css/

│ ├── js/

│ └── images/

├── includes/

├── admin/

├── public/

├── grafica-rapida-plugin.php

├── uninstall.php

└── README.md

Arquivo Principal (grafica-rapida-plugin.php):
Contém informações do plugin (cabeçalho)
Define constantes globais
Inclui as classes principais
Registra hooks de ativação e desativação
Inicia o plugin
Diretórios Principais:
includes/: Contém classes principais e funções de utilidade
admin/: Contém classes e funções relacionadas à área administrativa
public/: Contém classes e funções relacionadas à área pública do site
assets/: Armazena arquivos estáticos (CSS, JavaScript, imagens)
Desenvolvimento de Novas Funcionalidades:
a. Funcionalidades Administrativas:

  - Crie um novo arquivo PHP na pasta `admin/`
  - Nomeie o arquivo de forma descritiva, por exemplo: `class-grafica-rapida-admin-pedidos.php`
  - Defina uma nova classe dentro deste arquivo
  - Adicione um método `init()` à classe para configurar hooks e ações
  - Inclua e instancie esta nova classe em `includes/class-grafica-rapida-admin.php`
b. Funcionalidades Públicas:

  - Crie um novo arquivo PHP na pasta `public/`
  - Nomeie o arquivo de forma descritiva, por exemplo: `class-grafica-rapida-public-catalogo.php`
  - Defina uma nova classe dentro deste arquivo
  - Adicione um método `init()` à classe para configurar hooks e ações
  - Inclua e instancie esta nova classe em `includes/class-grafica-rapida-public.php`
c. Estilos e Scripts:

  - Adicione arquivos CSS específicos em `assets/css/`
  - Adicione arquivos JavaScript específicos em `assets/js/`
  - Enfileire estes arquivos nas respectivas classes usando `wp_enqueue_style()` e `wp_enqueue_script()`
Diretrizes de Codificação:
Use nomes de classes e métodos descritivos
Siga as convenções de nomenclatura do WordPress (use underscores para nomes de funções e métodos)
Comente o código adequadamente, explicando a funcionalidade de classes e métodos
Use indentação consistente (preferencialmente 4 espaços)
Evite a repetição de código, use funções auxiliares quando necessário
Adicionando Novas Páginas de Administração:
Crie um novo arquivo na pasta admin/ para a lógica da página
Adicione um novo item de menu em admin/class-grafica-rapida-admin-menu.php
Crie um método para renderizar o conteúdo da página
Adicionando Funcionalidades Públicas:
Crie um novo arquivo na pasta public/ para a lógica da funcionalidade
Use hooks e filtros do WordPress para integrar a funcionalidade ao tema
Gestão de Dependências:
Se a nova funcionalidade depender de outras partes do plugin, gerencie essas dependências cuidadosamente
Use injeção de dependência quando possível para manter o código modular
Tratamento de Erros e Depuração:
Use try/catch para capturar e tratar exceções
Implemente logs de erros para facilitar a depuração
Use constantes de depuração (como WP_DEBUG) para controlar a saída de informações de debug
Internacionalização:
- Use funções de internacionalização do WordPress (__(), _e(), etc.) para todo o texto visível
- Mantenha as strings de tradução no domínio do plugin definido no arquivo principal
Segurança:
- Valide e sanitize todas as entradas de usuário
- Use nonces para formulários administrativos
- Verifique as capacidades do usuário antes de executar ações administrativas
Performance:
- Otimize consultas ao banco de dados
- Use o cache do WordPress quando apropriado
- Minimize o uso de recursos em hooks frequentemente executados
Compatibilidade:
- Teste o plugin com diferentes versões do WordPress e PHP
- Mantenha a compatibilidade com o WooCommerce, se relevante
Documentação:
- Mantenha o arquivo README.md atualizado com informações sobre o plugin
- Documente funções e classes usando DocBlocks
- Inclua exemplos de uso para APIs ou hooks personalizados