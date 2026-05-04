import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalUsuario.module.css";

function UsuarioModal({ dados, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    nome: "",
    email: "",
    senha: "",
    cpf: "",
    cargo: "",
  });
  const [editando, setEditando] = useState(false);

  useEffect(() => {
    if (dados) {
      setFormData(dados);
      setEditando(true);
      console.log("editando", editando);
    } else {
      setFormData({ nome: "", email: "", senha: "", cpf: "", cargo: "" });
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
    const { id_usuario, ...restoDados } = formData;
    onSubmit(restoDados, editando);
  };

  return (
    <Modal  show={show} onHide={handleClose}>
      <Form  className={style.modal} onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>{editando ? "Editar usuário" : "Criar novo"}</Modal.Title>
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
              <Form.Label>Email</Form.Label>
              <Form.Control
                name="email"
                value={formData.email}
                onChange={handleChange}
                required={!editando}
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Senha</Form.Label>
              <Form.Control
                name="senha"
                value={formData.senha}
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
              <Form.Label>Cargo</Form.Label>
              <Form.Select
                name="cargo"
                value={formData.cargo}
                onChange={handleChange}
                required={!editando}
              >
                <option value="">Selecione um cargo...</option>
                <option value="admin">Admin</option>
                <option value="ceremonialista">Ceremonialista</option>
              </Form.Select>
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
            {editando ? "Editar usuário" : "Criar novo"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default UsuarioModal;
