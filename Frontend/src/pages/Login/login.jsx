import { useState } from "react";
import {
  Form,
  Button,
  Card,
  Container,
  Row,
  Col,
  InputGroup,
} from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import style from "./login.module.css";
import { MdEmail } from "react-icons/md";
import { IoMdLock } from "react-icons/io";
import { FaEye } from "react-icons/fa";
import { FaEyeSlash } from "react-icons/fa";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [viewSenha, setViewSenha] = useState(false);
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();

    try {
      // Aqui você chamaria sua API
      // const res = await api.post("/login", { email, password });
      // localStorage.setItem("token", res.data.token);

      console.log("Logando com:", { email, password });

      // Simulação de sucesso:
      localStorage.setItem("token", "TOKEN_DE_TESTE_AQUI");
      navigate("/");
    } catch (err) {
      alert("Usuário ou senha inválidos");
    }
  };

  return (
    <div className={style.loginWrapper}>
      <Container>
        <Row className="justify-content-center align-items-center vh-100">
          <Col md={6} lg={4}>
            <Card className={`border-0 shadow-lg ${style.loginCard}`}>
              <Card.Body className="p-5">
                <div className="text-center mb-4">
                  <h2 className="fw-bold">Bem-vindo</h2>
                  <p className="text-muted">Acesse sua conta</p>
                </div>

                <Form onSubmit={handleLogin}>
                  <Form.Group className="mb-3">
                    <Form.Label>E-mail</Form.Label>
                    <InputGroup>
                      <InputGroup.Text className="bg-white border-end-0">
                        <MdEmail />
                      </InputGroup.Text>
                      <Form.Control
                        type="email"
                        placeholder="nome@email.com"
                        className="border-start-0 ps-0"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                      />
                    </InputGroup>
                  </Form.Group>

                  <Form.Group className="mb-4">
                    <Form.Label>Senha</Form.Label>
                    <InputGroup>
                      <InputGroup.Text className="bg-white border-end-0">
                        <IoMdLock />
                      </InputGroup.Text>
                      <Form.Control
                        type={viewSenha ? `text` : "password"}
                        placeholder="••••••••"
                        className="border-start-0 ps-0"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                      />
                      <Button onClick={() => setViewSenha(!viewSenha)}>
                          {viewSenha ? <FaEye /> : <FaEyeSlash />}
                        </Button>
                    </InputGroup>
                  </Form.Group>

                  <Button type="submit" className={`btn ${style.btnLogin}`}>
                    Entrar
                  </Button>
                </Form>
              </Card.Body>
            </Card>
            <p className="text-center mt-3 text-white-50">
              © 2026 Senac Wedding - Todos os direitos reservados.
            </p>
          </Col>
        </Row>
      </Container>
    </div>
  );
}

export default Login;
