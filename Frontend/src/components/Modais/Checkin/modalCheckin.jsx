import { useEffect, useState } from "react";
import { Button, Form, Modal, Stack } from "react-bootstrap";
import style from "./modalCheckin.module.css";

function CheckinModal({ convidados, handleClose, onSubmit, show }) {
  const [formData, setFormData] = useState({
    convidado_idconvidado: "",
  });

  useEffect(() => {
    setFormData({
      convidado_idconvidado: "",
    });
  }, [show]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (!name) return;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const { id_checkin, ...restoDados } = formData;
    onSubmit(restoDados);
  };

  return (
    <Modal style={{ zIndex: "1200" }} show={show} onHide={handleClose}>
      <Form className={style.modal} onSubmit={handleSubmit}>
        <Modal.Header closeButton>
          <Modal.Title>Criar checkin</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Stack gap={3}>
            <Form.Group>
              <Form.Label>Convidado</Form.Label>
              <Form.Select
                name="convidado_idconvidado"
                value={formData.convidado_idconvidado}
                onChange={handleChange}
                required
              >
                <option value="">Selecione um convidado</option>
                {convidados.map(convidado => (
                  <option value={convidado.id_convidado}>{convidado.nome} - {convidado.cpf}</option>
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
            Criar{" "}
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}

export default CheckinModal;
