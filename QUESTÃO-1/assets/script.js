async function searchBook() {
  const termoBusca = document.getElementById('bookSearch').value.trim();
  if (termoBusca === '') {
    alert("Digite um nome de livro.");
    return;
  }

  try {
    const resposta = await fetch(`https://openlibrary.org/search.json?title=${encodeURIComponent(termoBusca)}`);
    const resultado = await resposta.json();

    if (resultado.docs && resultado.docs.length > 0) {
      const primeiroLivro = resultado.docs[0];

      const tituloLivro = primeiroLivro.title || '';
      const autorLivro = Array.isArray(primeiroLivro.author_name)
        ? primeiroLivro.author_name.join(', ')
        : 'Autor desconhecido';
      const anoPublicacao = primeiroLivro.first_publish_year || '';

      document.getElementById('titulo').value = tituloLivro;
      document.getElementById('autor').value = autorLivro;
      document.getElementById('ano').value = anoPublicacao;
    } else {
      alert("Livro não encontrado.");
    }

  } catch (e) {
    console.log(e);
    alert("Erro ao buscar livro.");
  }
}
