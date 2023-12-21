# Feupbook

At the heart of our project vision for Feupbook is the unwavering commitment to creating an innovative and vibrant social network tailored specifically for the students of the Faculty of Engineering at the University of Porto (FEUP). We envision Feupbook as a dynamic and inclusive online ecosystem, revolutionizing the way FEUP students connect, share, and engage within their academic community.

## Project Components

- [ER: Requirements Specification](https://git.fe.up.pt/lbaw/lbaw2324/lbaw23141/-/wikis/er)
- [EBD: Database Specification](https://git.fe.up.pt/lbaw/lbaw2324/lbaw23141/-/wikis/ebd)
- [EAP: Architecture Specification and Prototype](https://git.fe.up.pt/lbaw/lbaw2324/lbaw23141/-/wikis/eap)
- [PA: Product and Presentation](https://git.fe.up.pt/lbaw/lbaw2324/lbaw23141/-/wikis/pa)

## Artefacts Checklist

- The artefacts checklist is available at: <https://docs.google.com/spreadsheets/d/1OzV-w0MtTzFsdeNgBcZfjLYpJvjCY748my262I3luAg/edit#gid=537406521>

## Usage

### 2.1. Administration Credentials

| Email | Password |
| -------- | -------- |
| gcostell0@simplemachines.org | admin123 |

### 2.2. User Credentials

| Type          | Username  | Password |
| ------------- | --------- | -------- |
| basic account | alice.smith@example.com | password2 |

## Docker command

Full Docker command to launch the image available in the group's GitLab Container Registry using the production database:
```
docker run -it -p 8000:80 --name=lbaw23141 -e DB_DATABASE="lbaw23141" -e DB_SCHEMA="lbaw23141" -e DB_USERNAME="lbaw23141" -e DB_PASSWORD="kJrVHWaX" git.fe.up.pt:5050/lbaw/lbaw2324/lbaw23141
```

## URL

URL to the product: http://lbaw23141.lbaw.fe.up.pt 

## Team

- Group member 1 Felipe Jacob De Jesus Ferreira, up202102359@up.pt
- Group member 2 Luís Miguel Lima Tavares, up202108662@up.pt
- Group member 3 Miguel Martins Leitão, up202108851@up.pt
- Group member 4 Rodrigo Campos Rodrigues, up202108847@up.pt

---
