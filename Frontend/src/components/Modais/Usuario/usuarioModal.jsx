import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./usuarioModal.module.css";

function UsuarioModal({ data, handleClose, onSubmit, show, editando }) {
  const [formData, setFormData] = useState({
    nome: "",
    email: "",
    cpf: "",
    cargo: "",
  });

  const [isEditing, setIsEditing] = useState(false);

  useEffect(() => {
    if (data) {
      setFormData(data);
      setIsEditing(true);
    } else {
      setFormData({ nome: "", email: "", cpf: "", cargo: "" });
      setIsEditing(false);
    }
  }, [data, show]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const { id_usuario, ...restoDados } = formData;
    onSubmit(restoDados, isEditing);
  };

  return (
    <Modal style={{ zIndex: "10000" }} show={show} onHide={handleClose}>
      <Form onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>{data ? "Editar usuário" : "Novo usuário"}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Stack gap={3}>
            <Form.Group>
              <Form.Label>Nome</Form.Label>
              <Form.Control
                name="nome"
                value={formData.nome}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Email</Form.Label>
              <Form.Control
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Cpf</Form.Label>
              <Form.Control
                name="cpf"
                value={formData.cpf}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Cargo</Form.Label>
              <Form.Select
                name="cargo"
                value={formData.cargo}
                onChange={handleChange}
                required
              >
                <option value="">Selecione um cargo...</option>
                <option value="Admin">Admin</option>
                <option value="Ceremonialista">Ceremonialista</option>
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
            {data ? "Salvar alterações" : "Criar novo"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default UsuarioModal;
