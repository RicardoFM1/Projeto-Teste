import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalConvidado.module.css";

function ConvidadoModalNovo ({ data, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    nome: "",
    sobrenome: "",
    email: "",
    cpf: "",
    telefone: "",
    confirmacao: "",
    categoria: ""
  });


 
    
  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    onSubmit(formData);
  };

  return (
    <Modal style={{ zIndex: "10000" }} show={show} onHide={handleClose}>
      <Form onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>Novo convidado</Modal.Title>
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
              <Form.Label>Sobrenome</Form.Label>
              <Form.Control
                name="sobrenome"
                value={formData.sobrenome}
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
              <Form.Label>Telefone</Form.Label>
              <Form.Control
                name="telefone"
                value={formData.telefone}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Confirmacao</Form.Label>
              <Form.Select
                name="confirmacao"
                value={formData.confirmacao}
                onChange={handleChange}
                required
              >
                <option value="">Selecione uma confirmacao...</option>
                <option value="confirmado">Confirmado</option>
                <option value="não confirmado">Não Confirmado</option>
                <option value="cancelado">Cancelado</option>
              </Form.Select>
            </Form.Group>
            <Form.Group>
              <Form.Label>Categoria</Form.Label>
              <Form.Control
                name="categoria"
                value={formData.categoria}
                onChange={handleChange}
                required
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
            {"Criar novo"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default ConvidadoModalNovo