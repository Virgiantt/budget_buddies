CREATE database IF NOT EXISTS expense;
USE expense;
-- tables
-- Table: Users
CREATE TABLE Users (
    U_id CHAR(6) NOT NULL,
    U_name VARCHAR(100) NOT NULL,
    U_email VARCHAR(100) NOT NULL,
    U_password VARCHAR(255) NOT NULL,
    U_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    U_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT Users_pk PRIMARY KEY (U_id),
    CONSTRAINT Users_email_unique UNIQUE (U_email)
);
-- Table: Accounts
CREATE TABLE Accounts (
    A_id char(6) NOT NULL,
    A_name varchar(100) NOT NULL,
    A_account_type varchar(50) NOT NULL,
    A_initial_balance decimal(15,2) NOT NULL,
    A_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    A_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Users_U_id char(6) NOT NULL,
    CONSTRAINT Accounts_pk PRIMARY KEY (A_id),
    CONSTRAINT Accounts_Users FOREIGN KEY (Users_U_id)
        REFERENCES Users (U_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Table: Categories
CREATE TABLE Categories (
    C_id char(6) NOT NULL,
    C_name varchar(100) NOT NULL,
    C_type varchar(50) NOT NULL,
    C_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    C_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT Categories_pk PRIMARY KEY (C_id)
);

-- Table: Transactions
CREATE TABLE Transactions (
    T_ID char(6) NOT NULL,
    T_title varchar(100) NOT NULL,
    T_type varchar(50) NOT NULL,
    T_amount decimal(15,2) NOT NULL,
    T_transaction_date date NOT NULL,
    T_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    T_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Accounts_A_id char(6) NOT NULL,
    Categories_C_id char(6) NOT NULL,
    CONSTRAINT Transactions_pk PRIMARY KEY (T_ID),
    CONSTRAINT Transactions_Accounts FOREIGN KEY (Accounts_A_id)
        REFERENCES Accounts (A_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT Transactions_Categories FOREIGN KEY (Categories_C_id)
        REFERENCES Categories (C_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Table: Budgets
CREATE TABLE Budgets (
    B_id char(6) NOT NULL,
    B_amount_limit decimal(15,2) NOT NULL,
    B_month int NOT NULL,
    B_year int NOT NULL,
    B_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    B_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Categories_C_id char(6) NOT NULL,
    CONSTRAINT Budgets_pk PRIMARY KEY (B_id),
    CONSTRAINT Budgets_Categories FOREIGN KEY (Categories_C_id)
        REFERENCES Categories (C_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- End of file.