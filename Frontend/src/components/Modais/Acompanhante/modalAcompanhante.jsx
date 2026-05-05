import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalAcompanhante.module.css";

function AcompanhanteModal({ convidados, dados, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    nome: "",
    sobrenome: "",
    cpf: "",
    idade: "",
    convidado_idconvidado: "",
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
        cpf: "",
        idade: "",
        convidado_idconvidado: "",
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
    onSubmit(formData, editando);
  };

  return (
    <Modal style={{ zIndex: "1200" }} show={show} onHide={handleClose}>
      <Form className={style.modal} onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>{editando ? "Editar Acompanhante" : "Criar Acompanhante"}</Modal.Title>
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
              <Form.Label>CPF</Form.Label>
              <Form.Control
                name="cpf"
                value={formData.cpf}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Idade</Form.Label>
              <Form.Control
                type="number"
                name="idade"
                value={formData.idade}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group>
              <Form.Label>Convidado</Form.Label>
              <Form.Select
                name="convidado_idconvidado"
                value={formData.convidado_idconvidado}
                onChange={handleChange}
                required
              >
                <option value="">Selecione um convidado</option>
                {convidados.map((convidado) => (
                  <option key={convidado.id_convidado} value={convidado.id_convidado}>
                    {convidado.nome} - {convidado.cpf}
                  </option>
                ))}
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
            {editando ? "Salvar alterações" : "Criar"}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default AcompanhanteModal;
