import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalConvidado.module.css";

function ConvidadoModal ({ dados, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    nome: "",
    sobrenome: "",
    email: "",
    cpf: "",
    telefone: "",
    confirmacao: "",
    categoria: "",
    mesa_idmesa: null
  });

  const [editando, setEditando] = useState(false);

  useEffect(() => {
    if (dados) {
      setFormData(dados);
      setEditando(true);
    } else {
      setFormData({
        nome: "",
        sobrenome: "",
        email: "",
        cpf: "",
        telefone: "",
        confirmacao: "",
        categoria: "",
        mesa_idmesa: null
      });
      setEditando(false);
    }
  }, [dados, show]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const { id_checkin, ...restoDados } = formData;
    onSubmit(restoDados, editando);
  };

  return (
    <Modal style={{ zIndex: "1200" }} show={show} onHide={handleClose}>
      <Form className={style.modal} onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>{editando ? 'Editar convidado' : 'Novo convidado'}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Stack gap={3}>
            <Form.Group>
              <Form.Label>Nome</Form.Label>
              <Form.Control
                name="nome"
                value={formData.nome}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Sobrenome</Form.Label>
              <Form.Control
                name="sobrenome"
                value={formData.sobrenome}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Email</Form.Label>
              <Form.Control
                name="email"
                value={formData.email}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Cpf</Form.Label>
              <Form.Control
                name="cpf"
                value={formData.cpf}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Telefone</Form.Label>
              <Form.Control
                name="telefone"
                value={formData.telefone}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
              {editando ? 
            <Form.Group>
              <Form.Label>Confirmacao</Form.Label>
              <Form.Select
              name="confirmacao"
              value={formData.confirmacao}
              onChange={handleChange}
              required={!editando}
              >
                <option value="">Selecione uma confirmacao...</option>
                <option value="cancelado">Cancelado</option>
              </Form.Select>
            </Form.Group>
          : ''}
            <Form.Group>
              <Form.Label>Categoria</Form.Label>
              <Form.Control
                name="categoria"
                value={formData.categoria}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Numero da mesa (Opcional)</Form.Label>
              <Form.Control
              type="number"
                name="mesa_idmesa"
                value={formData.mesa_idmesa}
                onChange={handleChange}
              />
            </Form.Group>
          </Stack>
        </Modal.Body>
        <Modal.Footer>
          <Button
            className="ignorar-fonte-btn"
            variant="secondary"
            onClick={handleClose}
          >
            Cancelar
          </Button>
          <Button className={`btn ${style.btnSalvar}`} type="submit">
            {editando ? "Salvar alterações" : 'Criar'}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default ConvidadoModal