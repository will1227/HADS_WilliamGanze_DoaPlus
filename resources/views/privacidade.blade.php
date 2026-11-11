<x-guest-layout>
    {{-- Política de Privacidade (LGPD, RNF06): descreve o que o sistema faz de fato com cada dado --}}
    <h1 class="manchete">Política de Privacidade</h1>
    <p class="mt-4 max-w-[46ch] text-[1.0625rem] leading-normal text-cinza">
        O que o DoaPlus guarda sobre você, para quê e como apagar. Escrita conforme a Lei Geral de Proteção de Dados (Lei 13.709/2018).
    </p>

    <div class="mt-10 space-y-8 leading-relaxed [&_h2]:font-titulo [&_h2]:text-[1.0625rem] [&_h2]:font-bold [&_li]:mt-1 [&_p]:mt-2 [&_ul]:mt-2 [&_ul]:list-disc [&_ul]:pl-5">
        <section>
            <h2>Quais dados guardamos</h2>
            <ul>
                <li>Nome, e-mail, CPF e senha (a senha é guardada criptografada, nem a equipe consegue lê-la).</li>
                <li>Data de nascimento, sexo e peso.</li>
                <li>Tipo sanguíneo e histórico de doações e agendamentos. São <strong>dados sensíveis</strong> de saúde.</li>
                <li>Se você quiser: sua localização e as inscrições de notificação de cada aparelho.</li>
            </ul>
        </section>

        <section>
            <h2>Para que usamos</h2>
            <ul>
                <li>Calcular quando você pode doar de novo (idade, sexo e data da última doação, pelas regras do Ministério da Saúde).</li>
                <li>Agendar a doação no hemocentro escolhido.</li>
                <li>Avisar quando um hemocentro a até {{ \App\Models\Alerta::RAIO_KM }} km precisar do seu tipo sanguíneo (só com a localização informada).</li>
                <li>Somar pontos e liberar medalhas pelas doações.</li>
            </ul>
            <p>Não vendemos nem repassamos seus dados para empresas.</p>
        </section>

        <section>
            <h2>Quem vê seus dados</h2>
            <ul>
                <li>O hemocentro onde você agendou vê seu nome, tipo sanguíneo e sexo, para preparar o atendimento.</li>
                <li>Sua localização não aparece para ninguém: ela só é usada para calcular a distância até os hemocentros.</li>
                <li>Compartilhar no WhatsApp só acontece quando você toca no botão, e a mensagem não leva dados pessoais.</li>
                <li>Se você ativar as notificações, o aviso passa pelo serviço do seu navegador (Google, Apple, Mozilla ou Microsoft) criptografado.</li>
            </ul>
        </section>

        <section>
            <h2>Base legal</h2>
            <p>O seu consentimento, dado ao marcar a caixa no cadastro (art. 7º, I, e art. 11, I, da LGPD). Guardamos a data e a hora em que ele foi dado.</p>
        </section>

        <section>
            <h2>Seus direitos</h2>
            <ul>
                <li><strong>Ver e corrigir</strong> seus dados em "Meus dados" e no seu painel.</li>
                <li><strong>Remover a localização</strong> e <strong>desligar as notificações</strong> quando quiser, em "Meus dados".</li>
                <li><strong>Excluir a conta</strong> em "Meus dados" &gt; "Excluir minha conta". Isso apaga todos os seus dados do DoaPlus e retira o consentimento. Os registros oficiais das doações continuam com cada hemocentro, que tem obrigação legal de guardá-los.</li>
            </ul>
        </section>

        <section>
            <h2>Contato</h2>
            <p>Dúvidas sobre seus dados: <a href="mailto:contato@doaplus.com.br" class="underline underline-offset-4 hover:text-sangue">contato@doaplus.com.br</a>.</p>
        </section>
    </div>
</x-guest-layout>
