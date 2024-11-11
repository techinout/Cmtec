document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-user');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const userId = this.getAttribute('data-id');
            if (confirm('Tem certeza que deseja excluir este usuário?')) {
                fetch('../../../DAO/aluno/alunoDelete.php?codAluno=' + encodeURIComponent(userId), {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Aluno excluído com sucesso.') {
                        location.reload();
                    } else {
                        alert('Erro ao excluir o usuário.');
                    }
                })
                .catch(error => alert('Erro ao fazer a requisição.'));
            }
        });
    });
});
