<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# Registro de alterações para estudo (regra do projeto)

Toda alteração feita no código deve ser explicada em `ALTERACOES.txt`, na raiz do projeto (arquivo fora do Git, listado no `.gitignore`). O autor usa esse arquivo para estudar o código antes de apresentar o TCC.

- Ao terminar cada tarefa, acrescente ao final do arquivo uma seção com a data (`DD/MM/AAAA`) e a tarefa. Não reescreva nem apague seções antigas; se uma mudança nova altera algo já explicado, diga isso na seção nova.
- Para cada parte, explique em português simples: o problema ou requisito (citando HU/RF do DVP quando houver), o que foi feito, quais arquivos e métodos mudaram, por que essa solução foi escolhida e como foi testada.
- Explique os conceitos de Laravel/Tailwind usados que ainda não estão no glossário do arquivo e acrescente-os lá.
- Quando fizer sentido, inclua como demonstrar a funcionalidade e perguntas que a banca pode fazer, com resposta.
- Se o banco mudar, liste as migrations e lembre de atualizar o modelo lógico do DVP.
